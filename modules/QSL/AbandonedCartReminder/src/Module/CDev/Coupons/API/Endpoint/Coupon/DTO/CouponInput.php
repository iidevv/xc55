<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\CDev\Coupons\API\Endpoint\Coupon\DTO;

use CDev\Coupons\API\Endpoint\Coupon\DTO\CouponInput as ParentCouponInput;
use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class CouponInput extends ParentCouponInput
{
    /**
     * @Assert\Positive()
     * @var int|null
     */
    public ?int $abandoned_cart = null;

    /**
     * @var bool
     */
    public bool $abandoned_cart_coupon = false;
}
