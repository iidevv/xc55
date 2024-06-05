<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\OrderItem;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use Exception;
use XLite\API\Endpoint\Order\DTO\OrderItem\OrderItemOutput as OutputDTO;
use XLite\API\Endpoint\Order\Transformer\OrderItem\Surcharge\OutputTransformerInterface as SurchargeOutputTransformerInterface;
use XLite\Model\OrderItem;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected SurchargeOutputTransformerInterface $surchargeTransformer;

    public function __construct(
        SurchargeOutputTransformerInterface $surchargeTransformer
    ) {
        $this->surchargeTransformer = $surchargeTransformer;
    }

    /**
     * @param OrderItem $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->product_id = $object->getProduct()->getId();
        $dto->sku = $object->getSku();
        $dto->update_date = new DateTimeImmutable('@' . $object->getUpdateDate());
        $dto->name = $object->getName();
        $dto->price = $object->getPrice();
        $dto->item_net_price = $object->getItemNetPrice();
        $dto->discounted_subtotal = $object->getDiscountedSubtotal();
        $dto->amount = $object->getAmount();
        $dto->backordered_amount = $object->getBackorderedAmount();

        $dto->surcharges = [];
        foreach ($object->getSurcharges() as $surcharge) {
            $dto->surcharges[] = $this->surchargeTransformer->transform($surcharge, $to, $context);
        }

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof OrderItem;
    }
}
