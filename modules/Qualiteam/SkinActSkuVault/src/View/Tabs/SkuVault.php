<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\Tabs;

use Qualiteam\SkinActSkuVault\View\Settings as Settings;
use XCart\Extender\Mapping\ListChild;
use XLite\View\Tabs\ATabs;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class SkuVault extends ATabs
{
    const TAB_GENERAL          = 'skuvault_general';
    const TAB_PRODUCTS         = 'skuvault_products';
    const TAB_ORDERS           = 'skuvault_orders';
    const TAB_STATUSES_MAPPING = 'skuvault_statuses_mapping';
    const TAB_LOGS             = 'skuvault_logs';

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                static::TAB_GENERAL,
                static::TAB_PRODUCTS,
                static::TAB_ORDERS,
                static::TAB_STATUSES_MAPPING,
                static::TAB_LOGS,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function defineTabs()
    {
        return [
            static::TAB_GENERAL          => [
                'weight' => 100,
                'title'  => static::t('General Settings'),
                'widget' => Settings\GeneralSettings::class,
            ],
            static::TAB_PRODUCTS         => [
                'weight' => 200,
                'title'  => static::t('Products'),
                'widget' => Settings\Products::class,
            ],
            static::TAB_ORDERS           => [
                'weight' => 300,
                'title'  => static::t('Orders'),
                'widget' => Settings\Orders::class,
            ],
            static::TAB_STATUSES_MAPPING => [
                'weight' => 300,
                'title'  => static::t('Statuses mapping'),
                'widget' => Settings\StatusesMapping::class,
            ],
            static::TAB_LOGS             => [
                'weight' => 400,
                'title'  => static::t('Logs'),
                'widget' => Settings\Logs::class,
            ],
        ];
    }
}
