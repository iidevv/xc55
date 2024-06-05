<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\CDev\Coupons\Core\GA\DataMappers;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class Order extends \CDev\GoogleAnalytics\Core\GA\DataMappers\Order
{
    public function getPurchaseData(\XLite\Model\Order $order): array
    {
        $result = parent::getPurchaseData($order);

        /** @var \XLite\Model\Order|\CDev\Coupons\Model\Order $order */
        $coupons = $order->getUsedCoupons()->map(static function ($coupon) {
            return $coupon->getPublicCode();
        })->toArray();

        if ($coupons) {
            $result['coupon'] = implode(', ', $coupons);
        }

        return $result;
    }
}
