<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Order\DTO;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput as ExtendedOutput;
use CDev\Coupons\API\Endpoint\Order\DTO\UsedCoupon\OrderUsedCouponOutput as UsedCouponOutput;

/**
 * @Extender\Mixin
 */
class Output extends ExtendedOutput
{
    /**
     * @var UsedCouponOutput[]
     */
    public array $used_coupons = [];
}
