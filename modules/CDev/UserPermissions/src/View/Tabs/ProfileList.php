<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
class ProfileList extends \XLite\View\Tabs\ProfileList
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'roles'
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if (Auth::getInstance()->hasRootAccess()) {
            $list['roles'] = [
                'weight' => 200,
                'title'  => static::t('Roles'),
                'widget' => 'CDev\UserPermissions\View\ItemsList\Model\Roles',
            ];
        }

        return $list;
    }
}
