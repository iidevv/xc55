<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Feeds extends \XLite\View\Tabs\Feeds
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();

        $list[] = 'google_shopping_groups';
        $list[] = 'google_feed';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['google_shopping_groups'] = [
            'weight'     => 400,
            'title'      => static::t('Google product feed'),
            'references' => [
                ['target' => 'google_feed'],
            ],
            'widget'     => 'XC\GoogleFeed\View\Tabs\GoogleProductFeed'
        ];

        return $list;
    }
}
