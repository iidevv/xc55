<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Blocks\AddressForm;

use XCart\Extender\Mapping\ListChild;
use XC\FastLaneCheckout;

/**
 * Checkout Address form
 *
 * @ListChild (list="checkout_fastlane", weight="99999", zone="customer")
 */
class ShippingMetadata extends FastLaneCheckout\View\Blocks\AddressForm\Shipping
{
    /**
     * Check view visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        return [];
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return [];
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/metadata.twig';
    }
}
