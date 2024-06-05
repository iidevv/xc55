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
 * @Extender\Depend ({"QSL\ColorSwatches", "XC\ProductVariants"})
 */
class SelectProductVariants extends \XLite\View\Product\AttributeValue\Customer\Select
{
    protected function isVariantColorSwatches(): bool
    {
        return $this->getProduct()->mustHaveVariants()
            && $this->isColorSwatchesMode();
    }

    protected function getOptionTemplate()
    {
        return $this->isVariantColorSwatches()
            ? 'modules/QSL/ColorSwatches/product/attribute_value/select/variant_option.twig'
            : parent::getOptionTemplate();
    }

    protected function getSwatchAttributes(AttributeValueSelect $value): array
    {
        $attributes = parent::getSwatchAttributes($value);

        if (!$this->isAttributeValueAvailable($value)) {
            $key = array_search('cs-disabled', $attributes['class']);
            if ($key !== false) {
                unset($attributes['class'][$key]);
            }
            $attributes['class'][] = 'unavailable';
        }

        if ($this->isSelectedValue($value)) {
            $attributes['class'][] = 'selected';
        }

        return $attributes;
    }

    protected function getSelectClasses(): string
    {
        $classes = parent::getSelectClasses();

        if (
            $this->isColorSwatches()
            && $this->isShowSelector()
        ) {
            $attributeValue = $this->getAttributeValue();
            $selectedIds = $this->getSelectedIds();
            foreach ($attributeValue as $value) {
                if (
                    isset($selectedIds[$value->getAttribute()->getId()])
                    && $selectedIds[$value->getAttribute()->getId()] === $value->getId()
                ) {
                    if (!$value->isVariantAvailable()) {
                        $classes .= ' unavailable';
                    }
                    if ($value->isVariantStockWarning()) {
                        $classes .= ' stockwarning';
                    }
                    break;
                }
            }
        }

        return $classes;
    }
}
