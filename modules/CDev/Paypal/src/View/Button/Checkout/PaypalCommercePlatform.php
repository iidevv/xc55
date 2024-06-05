<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\Checkout;

use XCart\Extender\Mapping\ListChild;

/**
 * Express Checkout button
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="370")
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="100")
 */
class PaypalCommercePlatform extends \CDev\Paypal\View\Button\APaypalCommercePlatform
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $result = parent::getJSFiles();
        $result[] = 'modules/CDev/Paypal/button/paypal_commerce_platform/checkout.js';
        $result[] = 'modules/CDev/Paypal/button/paypal_commerce_platform/hosted_fields.js';

        return $result;
    }

    protected function isVisible()
    {
        return parent::isVisible() && !\XLite::getController()->isReturnedAfterPaypalCommercePlatform();
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/paypal_commerce_platform/checkout.twig';
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pcp-checkout';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'checkout';
    }
}
