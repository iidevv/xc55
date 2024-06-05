<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\View\HttpsSettings;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class Security extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'https_settings',
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'https_settings'       => [
                'weight' => 100,
                'title'  => static::t('HTTPS settings'),
                'widget' => HttpsSettings::class,
            ],
        ];
    }
}
