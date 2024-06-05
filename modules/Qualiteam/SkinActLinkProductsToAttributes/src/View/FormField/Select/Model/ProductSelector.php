<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\View\FormField\Select\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ProductSelector extends \XLite\View\FormField\Select\Model\ProductSelector
{
    /**
     * Defines the name of the text value input
     *
     * @return string
     */
    protected function getTextName()
    {
        return str_replace('linked_product_id', 'linked_product_name', $this->getParam(static::PARAM_NAME));
    }

    protected function isVisible()
    {
        $isVisible = !isset($this->attribute) ||
            (isset($this->attribute)
                && $this->attribute->getDisplayMode() !== \QSL\ColorSwatches\Model\Attribute::COLOR_SWATCHES_MODE);

        return $isVisible && parent::isVisible();
    }

    public function getCSSFiles()
    {
        $list =parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActLinkProductsToAttributes/ProductSelector.css';
        return $list;
    }
}