<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Tags;

use XLite\Model\Repo\Role;

/**
 * Roles
 */
class Roles extends \XLite\View\FormField\Select\Tags\ATags
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];

        foreach (Role::getAvailableRoles() as $role) {
            $list[$role->getId()] = $role->getPublicName();
        }

        return $list;
    }

    protected function getAttributes()
    {
        return array_merge(
            parent::getAttributes(),
            [
                'data-root-value' => \XLite\Core\Database::getRepo('XLite\Model\Role')->getRootId()
            ]
        );
    }
}
