<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

use XLite\Core\View\DynamicWidgetInterface;
use XLite\Model\WidgetParam\TypeObject;

/**
 * IsAllStockInCartCellClass dynamic widget renders all-stock-in-cart' css class on a product
 */
class IsAllStockInCartCellClass extends \XLite\View\AView implements DynamicWidgetInterface
{
    public const PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY = 'productStockAvailabilityPolicy';

    /**
     * Display widget with the default or overriden template.
     *
     * @param $template
     */
    protected function doDisplay($template = null)
    {
        if ($this->getProductStockAvailabililityPolicy()->isAllStockInCart($this->getCart())) {
            echo 'all-stock-in-cart';
        }
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
            static::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY => new TypeObject('Product stock availability policy'),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * @return \XLite\Model\Product\ProductStockAvailabilityPolicy
     */
    protected function getProductStockAvailabililityPolicy()
    {
        return $this->getParam(static::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY);
    }
}
