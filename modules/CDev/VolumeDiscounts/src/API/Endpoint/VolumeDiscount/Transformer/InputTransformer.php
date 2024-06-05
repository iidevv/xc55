<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\DTO\VolumeDiscountInput as InputDTO;
use CDev\VolumeDiscounts\Model\VolumeDiscount as Model;
use DateTimeImmutable;
use Exception;
use XLite\API\SubEntityInputTransformer\SubEntityIdBidirectionalCollectionInputTransformerInterface;
use XLite\API\SubEntityInputTransformer\SubEntityIdInputTransformerInterface;
use XLite\API\SubEntityOutputTransformer\SubEntityIdCollectionOutputTransformerInterface;
use XLite\API\SubEntityOutputTransformer\SubEntityIdOutputTransformerInterface;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected SubEntityIdBidirectionalCollectionInputTransformerInterface $zonesUpdater;

    protected SubEntityIdInputTransformerInterface $membershipUpdater;

    protected SubEntityIdOutputTransformerInterface $membershipIdOutputTransformer;

    protected SubEntityIdCollectionOutputTransformerInterface $zonesIdCollectionOutputTransformer;

    public function __construct(
        SubEntityIdBidirectionalCollectionInputTransformerInterface $zonesUpdater,
        SubEntityIdInputTransformerInterface $membershipUpdater,
        SubEntityIdOutputTransformerInterface $membershipIdOutputTransformer,
        SubEntityIdCollectionOutputTransformerInterface $zonesIdCollectionOutputTransformer
    ) {
        $this->zonesUpdater = $zonesUpdater;
        $this->membershipUpdater = $membershipUpdater;
        $this->membershipIdOutputTransformer = $membershipIdOutputTransformer;
        $this->zonesIdCollectionOutputTransformer = $zonesIdCollectionOutputTransformer;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $entity->setValue($object->value);
        $entity->setType($object->type);
        $entity->setSubtotalRangeBegin($object->subtotal_range_begin);
        $entity->setMembership($this->membershipUpdater->transform($object->membership));
        $this->zonesUpdater->update($entity->getZones(), $object->zones, $entity);
        $entity->setDateRangeBegin($object->date_range_begin ? $object->date_range_begin->getTimestamp() : 0);
        $entity->setDateRangeEnd($object->date_range_end ? $object->date_range_end->getTimestamp() : 0);

        if (
            $entity->getDateRangeBegin()
            && $entity->getDateRangeEnd()
            && $entity->getDateRangeBegin() > $entity->getDateRangeEnd()
        ) {
            throw new InvalidArgumentException('Field "date_range_begin" must be less than "date_range_end"');
        }

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && $context['input']['class'] === InputDTO::class;
    }

    /**
     * @return InputDTO
     * @throws Exception
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        $input = new InputDTO();
        $input->value = $entity->getValue();
        $input->type = $entity->getType();
        $input->subtotal_range_begin = $entity->getSubtotalRangeBegin();
        $input->membership = $this->membershipIdOutputTransformer->transform($entity->getMembership());
        $input->zones = $this->zonesIdCollectionOutputTransformer->transform($entity->getZones());
        $input->date_range_begin = $entity->getDateRangeBegin()
            ? new DateTimeImmutable('@' . $entity->getDateRangeBegin())
            : null;
        $input->date_range_end = $entity->getDateRangeEnd()
            ? new DateTimeImmutable('@' . $entity->getDateRangeEnd())
            : null;

        return $input;
    }
}
