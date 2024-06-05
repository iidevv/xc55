<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to payment settings
 * @Extender\Mixin
 */
class SettingsTabsVariants extends \XC\GoogleFeed\View\Tabs\SettingsTabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'google_shopping_groups';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();
        $tabs['google_shopping_groups'] = [
            'weight' => 50,
            'title'  => static::t('Configuration'),
            'widget' => 'XC\GoogleFeed\View\Admin\GoogleShoppingGroups',
        ];

        return $tabs;
    }
}
