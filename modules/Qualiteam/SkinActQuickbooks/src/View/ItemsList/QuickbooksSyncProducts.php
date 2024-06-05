<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\ItemsList;

use Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts;
use Qualiteam\SkinActQuickbooks\View\Tabs\QuickbooksSyncData;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Core\Request;

class QuickbooksSyncProducts extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = QuickbooksSyncData::TAB_PRODUCTS;

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
        
        $list[] = 'modules/Qualiteam/SkinActQuickbooks/sync_data/products.css';

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
            if (!in_array($columnName, ['category', 'name', 'price', 'qty']))
                continue;
            
            $result[$columnName] = $columnData;
        }
        
        $columns = $result;
        
        unset($columns['price'][static::COLUMN_CLASS]);
        unset($columns['qty'][static::COLUMN_CLASS]);
        
        $columns['variants'] = [
            static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Variants'),
            static::COLUMN_MAIN    => false,
            static::COLUMN_NO_WRAP => false,
            static::COLUMN_ORDERBY => 210,
            static::COLUMN_LINK    => 'quickbooks_sync_variants',
        ];
        
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
     * @param \XLite\Model\Product $entity Entity
     *
     * @return bool
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $productId = $entity ? $entity->getProductId() : null;
        
        if ($productId) {
            \XLite\Core\Database::getRepo(QuickbooksProducts::class)
                ->deleteProducts($productId);
            
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
    
    /**
     * Mark list as switchable (enable / disable)
     *
     * @return bool
     */
    protected function isSwitchable()
    {
        return false;
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return 'quickbooks_sync_products';
    }
    
    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return [
            'target' => 'quickbooks_sync_products',
        ];
    }
    
    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'XLite\View\SearchPanel\Product\Admin\Main';
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

        $result->{\XLite\Model\Repo\Product::SEARCH_QUICKBOOKS_PRODUCTS} = true;

        return $result;
    }
    
    /**
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'data-widget' => 'Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncProducts'
        ];
    }
    
    /**
     * Preprocess price
     *
     * @param mixed                $value  Value
     * @param array                $column Column data
     * @param \XLite\Model\Product $entity Product
     *
     * @return string
     */
    protected function preprocessPrice($value, array $column, \XLite\Model\Product $entity)
    {
        return \XLite\View\AView::formatPrice($value);
    }
    
    /**
     * Preprocess cost price
     *
     * @param mixed                $value  Value
     * @param array                $column Column data
     * @param \XLite\Model\Product $entity Product
     *
     * @return string
     */
    protected function preprocessCostPrice($value, array $column, \XLite\Model\Product $entity)
    {
        return \XLite\View\AView::formatPrice($value);
    }
    
    /**
     * Preprocess variants
     *
     * @param mixed                $value  Value
     * @param array                $column Column data
     * @param \XLite\Model\Product $entity Product
     *
     * @return string
     */
    protected function preprocessVariants($value, array $column, \XLite\Model\Product $entity)
    {
        $count = Database::getRepo(QuickbooksProducts::class)
            ->getProductSyncVariantsCount($entity);
        
        return ($count > 0 ? $count : '');
    }
}