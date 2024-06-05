<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\Product;

/**
 * Paypal Commerce Platform product page button
 */
class PaypalCommercePlatform extends \CDev\Paypal\View\Button\APaypalCommercePlatform
{
    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \CDev\Paypal\Main::isBuyNowEnabled();
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/paypal_commerce_platform/product.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pcp-product-page';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'product_page';
    }

    /**
     * @return string
     */
    protected function getButtonLayout()
    {
        return \XLite\Core\Request::isMobileDevice() ? 'vertical' : 'horizontal';
    }
}
