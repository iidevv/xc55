<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\View\Product\AttributeValue\Customer;

use Qualiteam\SkinActColorSwatchesFeature\Traits\ColorSwatchesTrait;
use XCart\Extender\Mapping\ListChild;

/**
 * Attribute value (Select)
 *
 * @ListChild (list="product.details.page.info.form", zone="customer", weight="10")
 */
class SelectColorSwatches extends \XLite\View\Product\AttributeValue\Customer\Select
{
    use ColorSwatchesTrait;

    protected array $attributes = [];

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate(): string
    {
        return $this->getModulePath() . '/product/attribute_value/select/selectbox-lite.twig';
    }

    public function getCSSFiles(): array
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/product/attribute_value/select/selectbox-lite.less';

        return $list;
    }

    public function isVisible()
    {
        return (
            \XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER
            && $this->isProductHasColorSwatchesAttribute()
        );
    }

    public function getColorSwatcheAttributes(): array
    {
        return $this->attributes;
    }

    protected function isProductHasColorSwatchesAttribute(): bool
    {
        foreach ($this->getProduct()->getEditableAttributes() as $attribute) {
            if ($attribute->isColorSwatchesAttribute()) {
                $this->attributes[$attribute->getId()] = $attribute;
            }
        }

        return !empty($this->attributes);
    }
}
