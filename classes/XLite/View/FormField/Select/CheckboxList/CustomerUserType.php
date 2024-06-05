<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList;

/**
 * User type selector
 */
class CustomerUserType extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    /**
     * Get user types
     *
     * @return array
     */
    protected function getUserTypes()
    {
        return [
            'C' => static::t('Registered Customers'),
            'N' => static::t('Anonymous Customers'),
        ];
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];

        $list['C'] = [
            'label' => static::t('Customer'),
            'options' => [],
        ];

        foreach ($this->getUserTypes() as $userType => $label) {
            $list['C']['options'][$userType] = $label;
        }

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('All user types');

        return $list;
    }
}
