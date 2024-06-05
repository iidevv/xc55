<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Checkout;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Payment template
 *
 * @Extender\Mixin
 */
abstract class Payment extends \XLite\View\Checkout\Payment implements \XLite\Base\IDecorator
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        if ($this->isXpaymentsMethodAvailable()) {
            $list[] = 'modules/XPay/XPaymentsCloud/checkout/widget.css';
        }
        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        if ($this->isXpaymentsMethodAvailable()) {
            $list[] = 'modules/XPay/XPaymentsCloud/checkout/widget.js';
            $list[] = 'modules/XPay/XPaymentsCloud/checkout/wallet_method.js';
        }
        return $list;
    }

    /**
     * Get SDK JS files
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        if ($this->isXpaymentsMethodAvailable()) {
            $list[static::RESOURCE_JS][] = 'modules/XPay/XPaymentsCloud/lib/js/widget.js';
        }
        return $list;
    }

    /**
     * Checks if X-Payments Cloud method is available in checkout
     *
     * @return bool
     */
    public function isXpaymentsMethodAvailable()
    {
        static $result = null;

        if (is_null($result)) {
            $result = false;
            foreach ($this->getCart()->getPaymentMethods() as $method) {
                if ('XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud' == $method->getClass()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Is "delayed payment checkout" functionality enabled (wrapper method)
     *
     * @return bool
     */
    protected function isDelayedPaymentEnabled()
    {
        return XPaymentsHelper::isDelayedPaymentEnabled();
    }

    /**
     * Get Card Setup amount
     *
     * @return mixed
     */
    protected function getCardSetupAmount()
    {
        return $this->executeCachedRuntime(function () {
            $result = null;
            try {
                $response = XPaymentsHelper::getClient()->doGetTokenizationSettings();
                $result = $response->tokenizeCardAmount;
            } catch (\XPaymentsCloud\ApiException $e) {
                XPaymentsHelper::log($e->getMessage());
            }

            return $result;
        });
    }

    /**
     * Get X-Payments cart total
     *
     * @return mixed
     */
    protected function getXpaymentsCartTotal()
    {
        return $this->isDelayedPaymentEnabled()
            ? $this->getCardSetupAmount()
            : $this->getCart()->getTotal();
    }
}
