<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Tabs;

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
        $list[] = 'product_feeds';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['product_feeds'] = [
            'weight' => 100,
            'title'  => static::t('Product feeds'),
            'widget' => 'QSL\ProductFeeds\View\ProductFeeds',
        ];

        return $list;
    }
}
