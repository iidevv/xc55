<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Shipping;

/**
 * Add shipping method popup button
 */
class AddMethod extends \XLite\View\Button\APopupButton
{
    public const PARAM_SHIPPING_METHOD_TYPE = 'shippingType';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SHIPPING_METHOD_TYPE => new \XLite\Model\WidgetParam\TypeString('Shipping methods type', ''),
        ];
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'button/js/shipping/add_method.js';

        $shippingTypes = new \XLite\View\Tabs\ShippingType();

        $list = array_merge($list, $shippingTypes->getJSFiles());

        return $list;
    }

    /**
     * Return shipping methods type which is provided to the widget
     *
     * @return string
     */
    protected function getShippingType()
    {
        return $this->getParam(static::PARAM_SHIPPING_METHOD_TYPE);
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target'       => 'shipping_method_selection',
            'widget'       => 'XLite\View\Shipping\AddMethod',
            'shippingType' => $this->getShippingType(),
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' add-shipping-method-button';
    }
}
