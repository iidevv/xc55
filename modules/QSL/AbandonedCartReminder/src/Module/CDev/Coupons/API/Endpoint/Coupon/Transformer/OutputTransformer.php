<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\CDev\Coupons\API\Endpoint\Coupon\Transformer;

use CDev\Coupons\API\Endpoint\Coupon\Transformer\OutputTransformer as ParentOutputTransformer;
use Exception;
use QSL\AbandonedCartReminder\Module\CDev\Coupons\API\Endpoint\Coupon\DTO\CouponOutput as CurrentOutputDTO;
use CDev\Coupons\API\Endpoint\Coupon\DTO\CouponOutput as OutputDTO;
use QSL\AbandonedCartReminder\Module\CDev\Coupons\Model\Coupon as CurrentModel;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class OutputTransformer extends ParentOutputTransformer
{
    /**
     * @param CurrentModel $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        /** @var CurrentOutputDTO $dto */
        $dto = parent::transform($object, $to, $context);

        $dto->abandoned_cart = $object->getAbandonedCart() ? $object->getAbandonedCart()->getOrderId() : null;
        $dto->abandoned_cart_coupon = $object->isAbandonedCartCoupon();

        return $dto;
    }
}
