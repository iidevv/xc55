<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\ColorSwatches\View\Product\AttributeValue\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Model\AttributeValue\AttributeValueSelect;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\ColorSwatches"})
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{
    protected function isColorSwatchesMode(): bool
    {
        return $this->isColorSwatches()
            && $this->getAttrValue();
    }

    protected function getColorSwatchesOptionTemplate(): string
    {
        return $this->isColorSwatchesMode()
            ? 'modules/QSL/ColorSwatches/product/attribute_value/select/block.twig'
            : parent::getOptionTemplate();
    }

    protected function getTemplate()
    {
        return (
            $this->isColorSwatchesMode()
            && \XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER
        )
            ? 'modules/QSL/ColorSwatches/product/attribute_value/select/blocks.twig'
            : parent::getTemplate();
    }

    protected function getSelectBoxTemplate(): string
    {
        return $this->isVariantsAttributes()
            ? 'modules/XC/ProductVariants/product/attribute_value/select/selectbox.twig'
            : 'product/attribute_value/select/selectbox.twig';
    }

    protected function getModifierTitle(AttributeValueSelect $value)
    {
        $result = parent::getModifierTitle($value);

        return str_replace(['(', ')'], '', $result);
    }

    protected function isVariantsAttributes(): bool
    {
        return method_exists($this->getProduct(), 'mustHaveVariants')
            ? $this->getProduct()->mustHaveVariants()
            : false;
    }

    protected function isAffectingAttribute(): bool
    {
        return method_exists(get_parent_class(), 'isAffectingAttribute')
            ? parent::isAffectingAttribute()
            : false;
    }
}
