<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\CDev\Coupons\API\Endpoint\Coupon\DTO;

use CDev\Coupons\API\Endpoint\Coupon\DTO\CouponOutput as ParentCouponOutput;
use Symfony\Component\Validator\Constraints as Assert;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class CouponOutput extends ParentCouponOutput
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
