<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\Tabs;

use Qualiteam\SkinActQuickbooks\View\SyncData;
use XCart\Extender\Mapping\ListChild;
use XLite\View\Tabs\ATabs;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class QuickbooksSyncData extends ATabs
{
    const TAB_CUSTOMERS       = 'quickbooks_sync_customers';
    const TAB_PRODUCTS        = 'quickbooks_sync_products';
    const TAB_VARIANTS        = 'quickbooks_sync_variants';
    const TAB_ORDERS          = 'quickbooks_sync_orders';
    const TAB_ERRORS          = 'quickbooks_sync_errors';

    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                static::TAB_ORDERS,
                static::TAB_PRODUCTS,
                static::TAB_VARIANTS,
                static::TAB_CUSTOMERS,
                static::TAB_ERRORS,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function defineTabs()
    {
        $tabs = [
            static::TAB_ORDERS   => [
                'weight'  => 100,
                'title'   => static::t('Quickbooks Synced Orders'),
                'widget'  => SyncData\Orders::class,
            ],
            static::TAB_PRODUCTS  => [
                'weight'  => 200,
                'title'   => static::t('Quickbooks Synced Products'),
                'widget'  => SyncData\Products::class,
            ],
            static::TAB_CUSTOMERS    => [
                'weight'  => 300,
                'title'   => static::t('Quickbooks Synced Customers'),
                'widget'  => SyncData\Customers::class,
            ],
            static::TAB_ERRORS   => [
                'weight'  => 400,
                'title'   => static::t('Quickbooks Sync Errors'),
                'widget'  => SyncData\Errors::class,
            ],
        ];
        
        if (\XLite\Core\Request::getInstance()->target == 'quickbooks_sync_variants') {
            unset($tabs[static::TAB_PRODUCTS]);
            $tabs[static::TAB_VARIANTS] = [
                'weight'  => 200,
                'title'   => static::t('Quickbooks Synced Products'),
                'widget'  => SyncData\Variants::class,
            ];
        }

        return $tabs;
    }
}