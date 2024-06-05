<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Product;

/**
 * Class ExpressCheckoutButton
 */
class ExpressCheckoutButton extends \XLite\View\Product\Details\Customer\Widget
{
    protected function isVisible()
    {
        return parent::isVisible() && \CDev\Paypal\Main::isExpressCheckoutEnabled($this->getCart());
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-paypal-ec-button';
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/product/details/express_checkout_widget.twig';
    }
}
