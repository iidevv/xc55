<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * QuickLook
 */
class QuickLook extends \XLite\View\Button\Regular
{
    /**
     * Widget param names
     */
    public const PARAM_PRODUCT = 'product';


    /**
     * getProduct
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Quick look';
    }

    /**
     * getClass
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' productid-' . $this->getProduct()->getProductId();
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return 'return void(0);';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT     => new \XLite\Model\WidgetParam\TypeObject(
                'Product',
                null,
                false,
                '\XLite\Model\Product'
            ),
        ];
    }
}
