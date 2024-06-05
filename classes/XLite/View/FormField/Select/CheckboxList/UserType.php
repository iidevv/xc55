<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList;

use XLite\Core\Auth;
use XLite\Model\Repo\Role;

/**
 * User type selector
 */
class UserType extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    /**
     * Get user types
     *
     * @return array
     */
    protected function getUserTypes()
    {
        $types = Auth::getInstance()->isPermissionAllowed('manage users')
            ? [
                'C' => static::t('Registered Customers'),
                'N' => static::t('Anonymous Customers'),
            ]
            : [];

        if (Auth::getInstance()->isPermissionAllowed('manage admins')) {
            $types['A'] = static::t('Administrator');
        }

        return $types;
    }

    /**
     * Get roles
     *
     * @return array
     */
    protected function getRoles()
    {
        $list = [];

        foreach (Role::getAvailableRoles() as $role) {
            $list[$role->getId()] = $role->getPublicName();
        }

        return $list;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];

        if (Auth::getInstance()->isPermissionAllowed('manage users')) {
            $list['C'] = [
                'label'   => static::t('Customer'),
                'options' => [],
            ];
        }

        foreach ($this->getUserTypes() as $userType => $label) {
            if ($userType === 'A') {
                $list[$userType] = [
                    'label' => $label,
                    'options' => $this->getRoles(),
                ];
            } elseif (isset($list['C'])) {
                $list['C']['options'][$userType] = $label;
            }
        }

        ksort($list);

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
