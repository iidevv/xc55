<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Payment\Base;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Payment processor
 *
 * @Extender\Mixin
 */
abstract class Processor extends \XLite\Model\Payment\Base\Processor implements \XLite\Base\IDecorator
{
    /**
     * Tokenization enabled flag
     *
     * @var bool
     */
    protected static $isTokenizationEnabled = null;

    /**
     * Check - payment processor is applicable for specified order or not
     *
     * @param \XLite\Model\Order $order Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        $isApplicable = parent::isApplicable($order, $method);

        if (
            $isApplicable
            && (
                $order->hasXpaymentsSubscriptions()
                || XPaymentsHelper::isDelayedPaymentEnabled()
            )
        ) {

            if ($method->isXpayments()) {

                if (null === static::$isTokenizationEnabled) {
                    try {
                        $response = XPaymentsHelper::getClient()->doGetTokenizationSettings();
                        static::$isTokenizationEnabled = (bool)$response->tokenizationEnabled;
                    } catch (\Exception $exception) {
                        XPaymentsHelper::log($exception->getMessage());
                    }
                }

                $isApplicable = static::$isTokenizationEnabled;

            } elseif (!XPaymentsHelper::isDelayedPaymentEnabled()) {

                $isApplicable = false;
            }
        }

        return $isApplicable;
    }
}
