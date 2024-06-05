<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Checkout;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Payment extends \XLite\View\Checkout\Payment
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (
            \CDev\Paypal\Main::isExpressCheckoutEnabled()
            || \CDev\Paypal\Main::isPaypalForMarketplacesEnabled()
            || \CDev\Paypal\Main::isPaypalCommercePlatformEnabled()
        ) {
            $list[] = 'modules/CDev/Paypal/checkout/payment.js';
        }

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(['service_name' => 'PayflowTransparentRedirect']);
        if ($method && $method->isEnabled()) {
            $list[] = 'modules/CDev/Paypal/transparent_redirect/payment.js';

            // Add JS file for dynamic credit card widget
            $list = array_merge($list, $this->getWidget([], 'XLite\View\CreditCard')->getJSFiles());
        }

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(['service_name' => 'PayflowTransparentRedirect']);
        if ($method && $method->isEnabled()) {
            //Add CSS file for dynamic credit card widget
            $list = array_merge($list, $this->getWidget([], 'XLite\View\CreditCard')->getCSSFiles());
        }

        return $list;
    }

    /**
     * Returns true if token initialized and is not expired
     *
     * @return boolean
     */
    protected function isTokenValid()
    {
        return !empty(\XLite\Core\Session::getInstance()->ec_token)
            && \CDev\Paypal\Model\Payment\Processor\ExpressCheckout::TOKEN_TTL
                > \XLite\Core\Converter::time() - \XLite\Core\Session::getInstance()->ec_date
            && !empty(\XLite\Core\Session::getInstance()->ec_payer_id);
    }
}
