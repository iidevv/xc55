<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\Product;

use XLite\Core\Config;

class AttributeQuantityBox extends \XLite\View\Product\QuantityBox
{

    /**
     * Widget param names
     */
    public const PARAM_PARENT_PRODUCT      = 'parent_product';

    /**
     * Get dir
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return 'modules/Qualiteam/SkinActLinkProductsToAttributes/product/attribute_qty_box.twig';
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
            static::PARAM_PARENT_PRODUCT      => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
        ];
    }
    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        $maxValue = parent::getMaxQuantity();

        $configMaxLimit = Config::getInstance()->Qualiteam->SkinActLinkProductsToAttributes->linkedProductMaxQty;

        if ($configMaxLimit > 0 && $this->getParam(static::PARAM_PARENT_PRODUCT) > 0) {
            $maxValue = min($maxValue, $this->getParentProduct()->getLinkedParentQty() * $configMaxLimit);
        }

        return $maxValue;
    }

    protected function getParentProduct()
    {
        return $this->getParam(static::PARAM_PARENT_PRODUCT);
    }
}