<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class SeoSettings extends \XLite\View\Tabs\SeoSettings
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'sitemap';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return array_merge(
            parent::defineTabs(),
            [
                'sitemap' => [
                    'weight' => 400,
                    'title'  => static::t('XML sitemap'),
                    'widget' => 'CDev\XMLSitemap\View\Admin\Sitemap',
                ],
            ]
        );
    }
}
