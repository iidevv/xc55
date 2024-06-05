<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model\Payment\Base;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Order;
use XLite\Model\Payment\Method;

/**
 * Payment processor
 *
 * @Extender\Mixin
 */
abstract class Processor extends \XLite\Model\Payment\Base\Processor
{
    /**
     * Check - payment processor is applicable for specified order or not
     *
     * @param Order          $order  Order
     * @param Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(Order $order, Method $method)
    {
        $applicableForSubscriptions = !$order->hasSubscriptions()
            || (
                XPayments::class == $method->getClass()
                && 'Y' == $method->getSetting('saveCards')
            )
            || (
                SavedCard::class == $method->getClass()
            );

        return $applicableForSubscriptions 
            && parent::isApplicable($order, $method);
    }
}
