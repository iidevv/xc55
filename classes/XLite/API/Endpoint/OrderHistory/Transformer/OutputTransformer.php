<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\OrderHistory\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use Exception;
use XLite\API\Endpoint\OrderHistory\DTO\OrderHistoryOutput as OutputDTO;
use XLite\API\Endpoint\OrderHistory\Transformer\Detail\OutputTransformerInterface as DetailOutputTransformerInterface;
use XLite\Model\OrderHistoryEvents;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected DetailOutputTransformerInterface $detailTransformer;

    public function __construct(
        DetailOutputTransformerInterface $detailTransformer
    ) {
        $this->detailTransformer = $detailTransformer;
    }

    /**
     * @param OrderHistoryEvents $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getEventId();
        $dto->comment = $object->getCode();
        $dto->description = $object->getDescription();
        $dto->ip = $object->getAuthorIp();
        $dto->profile_id = $object->getAuthor() ? $object->getAuthor()->getProfileId() : null;
        $dto->profile_name = $object->getAuthorName();
        $dto->data = $object->getData() ?: [];
        $dto->date = new DateTimeImmutable('@' . $object->getDate());
        $dto->code = $object->getCode();

        $dto->details = [];
        foreach ($object->getDetails() as $detail) {
            $dto->details[] = $this->detailTransformer->transform($detail, $to, $context);
        }

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof OrderHistoryEvents;
    }
}
