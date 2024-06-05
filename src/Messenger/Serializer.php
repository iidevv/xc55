<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger;

use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;
use Symfony\Component\Messenger\Stamp\SerializerStamp;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;
use XLite\Core\Database;
use XLite\Model\AEntity;

class Serializer implements SerializerInterface
{
    public const MESSENGER_SERIALIZATION_CONTEXT = 'messenger_serialization';
    private const STAMP_HEADER_PREFIX = 'X-Message-Stamp-';

    private $serializer;
    private $format;
    private $context;

    public function __construct(SymfonySerializerInterface $serializer = null, string $format = 'json', array $context = [])
    {
        $this->serializer = $serializer ?? self::create()->serializer;
        $this->format = $format;
        $this->context = $context + [self::MESSENGER_SERIALIZATION_CONTEXT => true];
    }

    public static function create(): self
    {
        if (!class_exists(SymfonySerializer::class)) {
            throw new LogicException(sprintf('The "%s" class requires Symfony\'s Serializer component. Try running "composer require symfony/serializer" or use "%s" instead.', __CLASS__, PhpSerializer::class));
        }

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer()];
        $serializer = new SymfonySerializer($normalizers, $encoders);

        return new self($serializer);
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        if (empty($encodedEnvelope['body']) || empty($encodedEnvelope['headers'])) {
            throw new MessageDecodingFailedException('Encoded envelope should have at least a "body" and some "headers".');
        }

        if (empty($encodedEnvelope['headers']['type'])) {
            throw new MessageDecodingFailedException('Encoded envelope does not have a "type" header.');
        }

        $stamps = $this->decodeStamps($encodedEnvelope);
        $serializerStamp = $this->findFirstSerializerStamp($stamps);

        $context = $this->context;
        if ($serializerStamp !== null) {
            $context = $serializerStamp->getContext() + $context;
        }

        try {
            $message = $this->serializer->deserialize($encodedEnvelope['body'], $encodedEnvelope['headers']['type'], $this->format, $context);
            $message = $this->deserializeModels($message);
        } catch (ExceptionInterface $e) {
            throw new MessageDecodingFailedException('Could not decode message: ' . $e->getMessage(), $e->getCode(), $e);
        }

        return new Envelope($message, $stamps);
    }

    public function encode(Envelope $envelope): array
    {
        $context = $this->context;
        /** @var SerializerStamp|null $serializerStamp */
        if ($serializerStamp = $envelope->last(SerializerStamp::class)) {
            $context = $serializerStamp->getContext() + $context;
        }

        $envelope = $envelope->withoutStampsOfType(NonSendableStampInterface::class);

        $headers = ['type' => \get_class($envelope->getMessage())] + $this->encodeStamps($envelope) + $this->getContentTypeHeader();

        return [
            'body' => $this->serializer->serialize($this->serializeModels($envelope->getMessage()), $this->format, $context),
            'headers' => $headers,
        ];
    }

    private function decodeStamps(array $encodedEnvelope): array
    {
        $stamps = [];
        foreach ($encodedEnvelope['headers'] as $name => $value) {
            if (!str_starts_with($name, self::STAMP_HEADER_PREFIX)) {
                continue;
            }

            try {
                $stamps[] = $this->serializer->deserialize($value, substr($name, \strlen(self::STAMP_HEADER_PREFIX)) . '[]', $this->format, $this->context);
            } catch (ExceptionInterface $e) {
                throw new MessageDecodingFailedException('Could not decode stamp: ' . $e->getMessage(), $e->getCode(), $e);
            }
        }
        if ($stamps) {
            $stamps = array_merge(...$stamps);
        }

        return $stamps;
    }

    private function encodeStamps(Envelope $envelope): array
    {
        if (!$allStamps = $envelope->all()) {
            return [];
        }

        $headers = [];
        foreach ($allStamps as $class => $stamps) {
            $headers[self::STAMP_HEADER_PREFIX . $class] = $this->serializer->serialize($stamps, $this->format, $this->context);
        }

        return $headers;
    }

    /**
     * @param StampInterface[] $stamps
     */
    private function findFirstSerializerStamp(array $stamps): ?SerializerStamp
    {
        foreach ($stamps as $stamp) {
            if ($stamp instanceof SerializerStamp) {
                return $stamp;
            }
        }

        return null;
    }

    private function getContentTypeHeader(): array
    {
        $mimeType = $this->getMimeTypeForFormat();

        return $mimeType === null ? [] : ['Content-Type' => $mimeType];
    }

    private function getMimeTypeForFormat(): ?string
    {
        switch ($this->format) {
            case 'json':
                return 'application/json';
            case 'xml':
                return 'application/xml';
            case 'yml':
            case 'yaml':
                return 'application/x-yaml';
            case 'csv':
                return 'text/csv';
        }

        return null;
    }

    private function serializeModels($message)
    {
        $properties = (new ReflectionClass($message))->getProperties();

        foreach ($properties as $property) {
            $property->setValue($message, $this->getSerializedPropertyValue(
                $this->getPropertyValue($property, $message)
            ));
        }

        return $message;
    }

    private function deserializeModels($message)
    {
        foreach ((new ReflectionClass($message))->getProperties() as $property) {
            $property->setValue($message, $this->getRestoredPropertyValue(
                $this->getPropertyValue($property, $message)
            ));
        }

        return $message;
    }

    /**
     * Get the property value for the given property.
     *
     * @param ReflectionProperty $property
     * @param                    $message
     *
     * @return mixed
     */
    protected function getPropertyValue(ReflectionProperty $property, $message)
    {
        $property->setAccessible(true);

        return $property->getValue($message);
    }

    /**
     * Get the property value prepared for serialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getSerializedPropertyValue($value)
    {
        if ($value instanceof AEntity) {
            return ['__xlite_model__' => get_class($value), 'id' => $value->getUniqueIdentifier()];
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $key => $item) {
                $result[$key] = $this->getSerializedPropertyValue($item);
            }
            $value = $result;
        }

        return $value;
    }

    /**
     * Get the restored property value after deserialization.
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function getRestoredPropertyValue($value)
    {
        if (is_array($value)) {
            if (isset($value['__xlite_model__'])) {
                $repo = $value['__xlite_model__'] === 'XLite\Model\Cart' ? 'XLite\Model\Order' : $value['__xlite_model__'];
                return Database::getRepo($repo)->find($value['id']);
            } else {
                $result = [];
                foreach ($value as $key => $item) {
                    $result[$key] = $this->getRestoredPropertyValue($item);
                }
                $value = $result;
            }
        }

        return $value;
    }
}
