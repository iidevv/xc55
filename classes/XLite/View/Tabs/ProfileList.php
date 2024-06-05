<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to user profile section
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class ProfileList extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'profile_list';

        if (\XLite\Core\Request::getInstance()->section !== 'Communications') {
            $list[] = 'memberships';
        }
        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'profile_list' => [
                'weight'   => 100,
                'title'    => static::t('All users'),
                'widget'   => 'XLite\View\ItemsList\Model\Profile'
            ],
            'memberships' => [
                'weight'   => 300,
                'title'    => static::t('Memberships'),
                'template' => 'customer_profiles/memberships.twig',
            ]
        ];
    }
}
