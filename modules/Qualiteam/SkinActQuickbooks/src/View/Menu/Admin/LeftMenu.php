<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Left side menu widget
 * 
 * @Extender\Mixin
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->addRelatedTarget(
            'quickbooks_sync_orders',
            'quickbooks_sync_data'
        );
        $this->addRelatedTarget(
            'quickbooks_sync_products',
            'quickbooks_sync_data'
        );
        $this->addRelatedTarget(
            'quickbooks_sync_variants',
            'quickbooks_sync_data'
        );
        $this->addRelatedTarget(
            'quickbooks_sync_customers',
            'quickbooks_sync_data'
        );
        $this->addRelatedTarget(
            'quickbooks_sync_errors',
            'quickbooks_sync_data'
        );
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();
        
        $items['sales'][static::ITEM_CHILDREN]['quickbooks_sync_data'] = [
            static::ITEM_TITLE      => static::t('QuickBooks Sync Data'),
            static::ITEM_TARGET     => 'quickbooks_sync_data',
            static::ITEM_PERMISSION => 'manage orders',
            static::ITEM_WEIGHT     => 600,
        ];
        
        return $items;
    }
}