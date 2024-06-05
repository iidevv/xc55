<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

use XLite\Core\View\DynamicWidgetInterface;
use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeObject;
use XLite\Model\WidgetParam\TypeString;

/**
 * ChangeQty dynamic widget renders 'change quantity' block on a product in an items list.
 */
class ChangeQty extends \XLite\View\AView implements DynamicWidgetInterface
{
    public const PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY = 'productStockAvailabilityPolicy';
    public const PARAM_PRODUCT_ID                        = 'productId';
    public const PARAM_ALL_STOCK_IN_CART_TOOLTIP_TEXT    = 'allStockInCartTooltipText';

    protected function getDefaultTemplate()
    {
        return 'items_list/product/parts/common.change-qty-block.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT_ID                        => new TypeInt('Product id'),
            static::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY => new TypeObject('Product stock availability policy'),
            static::PARAM_ALL_STOCK_IN_CART_TOOLTIP_TEXT    => new TypeString('All stock in cart tooltip text'),
        ];
    }

    /**
     * @return \XLite\Model\Product\ProductStockAvailabilityPolicy
     */
    protected function getProductStockAvailabilityPolicy()
    {
        return $this->getParam(static::PARAM_PRODUCT_STOCK_AVAILABILITY_POLICY);
    }

    /**
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getParam(static::PARAM_PRODUCT_ID);
    }

    /**
     * @return string
     */
    protected function getAllStockInCartTooltipText()
    {
        return $this->getParam(static::PARAM_ALL_STOCK_IN_CART_TOOLTIP_TEXT);
    }
}
