<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Price extends \XLite\View\Price
{

    /**
     * Widget parameter names
     */
    public const PARAM_LINKED_ITEM_QTY = 'linked_product_qty';

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
        ];
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $product = parent::getProduct();

        $product->setLinkedProductQtyString($this->getParam(static::PARAM_LINKED_ITEM_QTY));

        return $product;
    }

    /**
     * Return net price of product
     *
     * @return float
     */
    protected function getNetPrice($value = null)
    {
        $mainPrice = $this->getProduct()->getDisplayPrice();

        if ($this->getProduct()->getAttrValues()) {
            foreach ($this->getProduct()->getAttrValues() as $value) {
                if ($value->getLinkedProduct()) {
                    $qty = $this->getProduct()->getLinkedProductQty($value->getAttribute()->getId());

                    if ($qty > 1) {
                        $mainPrice += $value->getAbsoluteValue('price') * ($qty - 1);
                    }
                }
            }
        }

        return $mainPrice;
    }
}