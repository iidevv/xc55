<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Order\Transformer\UsedCoupon;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use CDev\Coupons\API\Endpoint\Order\DTO\UsedCoupon\OrderUsedCouponOutput as OutputDTO;
use CDev\Coupons\Model\UsedCoupon;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param UsedCoupon $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->code = $object->getCode();
        $dto->value = $object->getValue();
        $dto->type = $object->getType();
        $dto->coupon_id = $object->getCoupon() ? $object->getCoupon()->getId() : null;

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof UsedCoupon;
    }
}
