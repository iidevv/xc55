<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class EditableAttributes extends \XLite\View\Product\Details\Customer\EditableAttributes
{
    /**
     * Widget parameter names
     */
    public const PARAM_LINKED_ITEM_QTY = 'linked_product_qty';
    public const PARAM_QUANTITY = 'parent_product_quantity';

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $product = parent::getProduct();

        $product->setLinkedParentQty($this->getParam(static::PARAM_QUANTITY));
        $product->setLinkedProductQtyString($this->getParam(static::PARAM_LINKED_ITEM_QTY));

        return $product;
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
            static::PARAM_LINKED_ITEM_QTY => new \XLite\Model\WidgetParam\TypeInt('Linked product quantity', 1),
            static::PARAM_QUANTITY => new \XLite\Model\WidgetParam\TypeInt('Product quantity', 1),
        ];
    }

    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = md5(mt_rand() . microtime());
        return $list;
    }


}