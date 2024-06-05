<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\Tabs;

class SettingsTabs extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'google_feed';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'google_feed' => [
                'weight' => 100,
                'title'  => static::t('Generation'),
                'widget' => 'XC\GoogleFeed\View\Admin\GoogleFeed',
            ],
        ];
    }
}
