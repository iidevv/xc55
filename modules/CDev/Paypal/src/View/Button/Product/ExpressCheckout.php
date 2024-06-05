<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\Product;

/**
 * Express Checkout button
 */
class ExpressCheckout extends \CDev\Paypal\View\Button\AExpressCheckout
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
            'modules/CDev/Paypal/button/js/form.js',
            'modules/CDev/Paypal/button/js/button_buy_now.js'
        ]);
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pp-style-buynow pp-ec-form' . (
            \CDev\Paypal\Main::isPaypalCreditEnabled()
                ? ' pp-funding-credit'
                : ''
            );
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
