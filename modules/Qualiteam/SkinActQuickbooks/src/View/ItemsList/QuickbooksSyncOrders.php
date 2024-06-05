<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\ItemsList;

use Qualiteam\SkinActQuickbooks\Model\QuickbooksOrders;
use Qualiteam\SkinActQuickbooks\View\Tabs\QuickbooksSyncData;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Core\Request;

class QuickbooksSyncOrders extends \XLite\View\ItemsList\Model\Order\Admin\Search
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = QuickbooksSyncData::TAB_ORDERS;

        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();
        
        $result = [];
        
        foreach ($columns as $columnName => $columnData) {
            if (
                !in_array(
                    $columnName,
                    [
                        'orderNumber',
                        'date',
                        'profile',
                        'paymentStatus',
                        'shippingStatus',
                        'total'
                    ]
                )
            ) {
                continue;
            }
            
            $result[$columnName] = $columnData;
        }
        
        $columns = $result;

        unset($columns['paymentStatus'][static::COLUMN_CLASS]);
        unset($columns['paymentStatus'][static::COLUMN_SORT]);        
        $columns['paymentStatus'][static::COLUMN_TEMPLATE] =
            'modules/Qualiteam/SkinActQuickbooks/sync_data/orders/payment_status.twig';
        
        unset($columns['shippingStatus'][static::COLUMN_CLASS]);
        unset($columns['shippingStatus'][static::COLUMN_SORT]);
        $columns['shippingStatus'][static::COLUMN_TEMPLATE] =
            'modules/Qualiteam/SkinActQuickbooks/sync_data/orders/shipping_status.twig';
        
        return $columns;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }
    
    /**
     * @param \XLite\Model\Profile $entity Entity
     *
     * @return bool
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $orderId = $entity ? $entity->getOrderId() : null;
        
        if ($orderId) {
            \XLite\Core\Database::getRepo(QuickbooksOrders::class)
                ->deleteOrders($orderId);
            
            return true;
        }

        return false;
    }

    
    /**
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    protected function getRightActions()
    {
        return [];
    }

    protected function getLeftActions()
    {
        return parent::getLeftActions();
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_NONE;
    }
    
    /**
     * @return string
     */
    protected function getCreateURL()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return '';
    }

    /**
     * @return boolean
     */
    protected function isExportable()
    {
        return false;
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return 'quickbooks_sync_orders';
    }
    
    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return [
            'target' => 'quickbooks_sync_orders',
        ];
    }
    
    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XLite\View\SearchPanel\Order\Admin\Main';
    }
    
    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'Qualiteam\SkinActQuickbooks\View\StickyPanel\QuickbooksSyncData';
    }
    
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Order::SEARCH_QUICKBOOKS_ORDERS} = true;

        return $result;
    }
    
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/search_container.twig';
    }
    
    /**
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'data-widget' => 'Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncOrders'
        ];
    }
}