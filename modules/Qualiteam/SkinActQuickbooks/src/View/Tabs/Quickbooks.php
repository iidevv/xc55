<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\Tabs;

use Qualiteam\SkinActQuickbooks\View\Settings as Settings;
use XCart\Extender\Mapping\ListChild;
use XLite\View\Tabs\ATabs;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Quickbooks extends ATabs
{
    const TAB_GENERAL          = 'quickbooks_general';
    const TAB_QWC_FILES        = 'quickbooks_qwc_files';
    const TAB_SETTINGS         = 'quickbooks_settings';
    const TAB_ORDER_STATUSES   = 'quickbooks_order_statuses';

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                static::TAB_GENERAL,
                static::TAB_QWC_FILES,
                static::TAB_SETTINGS,
                static::TAB_ORDER_STATUSES,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function defineTabs()
    {
        return [
            static::TAB_GENERAL    => [
                'weight'  => 100,
                'title'   => static::t('Web Connector authentication'),
                'widget'  => Settings\GeneralSettings::class,
                'tooltip' => static::t('Web Connector authentication tooltip'),
            ],
            static::TAB_QWC_FILES  => [
                'weight'  => 200,
                'title'   => static::t('Web Connector (.QWC) Files'),
                'widget'  => Settings\QwcFiles::class,
                'tooltip' => static::t('Web Connector (.QWC) Files tooltip'),
            ],
            static::TAB_SETTINGS   => [
                'weight'  => 300,
                'title'   => static::t('Web Connector settings'),
                'widget'  => Settings\Settings::class,
                'tooltip' => static::t('Web Connector settings tooltip'),
            ],
            static::TAB_ORDER_STATUSES   => [
                'weight'  => 400,
                'title'   => static::t('Orders to be imported settings'),
                'widget'  => Settings\OrderStatuses::class,
                'tooltip' => static::t('Orders to be imported settings tooltip'),
            ],
        ];
    }
}