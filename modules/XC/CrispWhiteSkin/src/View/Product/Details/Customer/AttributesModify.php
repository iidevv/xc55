<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product attributes modify
 * @Extender\Mixin
 */
class AttributesModify extends \XLite\View\Product\Details\Customer\AttributesModify
{
    /**
     * Return specific CSS class for dialog wrapper
     *
     * @param $attribute \XLite\Model\Attribute
     *
     * @return string
     */
    protected function getAttributeCSSClass($attribute)
    {
        $class = parent::getAttributeCSSClass($attribute);
        $class .= ' ' . $this->getAssociatedAttributeTypeName($attribute->getType());

        if ($attribute->getType() == \XLite\Model\Attribute::TYPE_SELECT) {
            $class .= ' focused';
        }

        return $class;
    }

    /**
     * @param $letter
     *
     * @return string
     */
    protected function getAssociatedAttributeTypeName($letter)
    {
        $attribute_type_name = '';
        switch ($letter) {
            case 'T':
                $attribute_type_name = 'type-text';
                break;
            case 'C':
                $attribute_type_name = 'type-checkbox';
                break;
            case 'S':
                $attribute_type_name = 'type-select';
                break;
            case 'H':
                $attribute_type_name = 'type-hidden';
                break;
        }

        return $attribute_type_name;
    }
}
