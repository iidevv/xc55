<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Button\ProductList;

/**
 * Express Checkout button
 */
class ExpressCheckout extends \CDev\Paypal\View\Button\AExpressCheckout
{
    public const PARAM_PRODUCT_ID = 'productId';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\TypeInt('Product id'),
        ];
    }

    /**
     * @return bool
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
            'modules/CDev/Paypal/button/js/product_list.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pp-style-buynow pp-ec-product' . (
            \CDev\Paypal\Main::isPaypalCreditEnabled()
                ? ' pp-funding-credit'
                : ''
            );
    }

    /**
     * @return array
     */
    protected function getButtonAdditionalParams()
    {
        return parent::getButtonAdditionalParams() + [
                'data-product-id' => $this->getParam(static::PARAM_PRODUCT_ID),
            ];
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'product_list';
    }

    /**
     * @return string
     */
    protected function getButtonLayout()
    {
        return 'horizontal';
    }
}
