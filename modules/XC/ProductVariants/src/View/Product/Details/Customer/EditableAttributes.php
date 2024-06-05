<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Editable product attributes widget
 * @Extender\Mixin
 */
class EditableAttributes extends \XLite\View\Product\Details\Customer\EditableAttributes
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $attrs_values = [];
        foreach ($this->getAttributeValues() as $attribute) {
            $attrs_values[] = $this->getCacheParamByAttribute($attribute);
        }
        $list[] = implode(';', $attrs_values);

        return $list;
    }

    /**
     * @param $attribute
     *
     * @return string
     */
    protected function getCacheParamByAttribute($attribute)
    {
        $attributeObj = is_array($attribute) && isset($attribute['attributeValue'])
            ? $attribute['attributeValue']
            : $attribute;

        return $attributeObj instanceof \XLite\Model\AttributeValue\AAttributeValue
            ? $attributeObj->asString()
            : md5(serialize($attributeObj));
    }
    /**
     * Prepare template display
     *
     * @param string $template Template short path
     *
     * @return array
     */
    protected function prepareTemplateDisplay($template)
    {
        $this->getProduct()->setAttrValues($this->getAttributeValues());

        return parent::prepareTemplateDisplay($template);
    }
}
