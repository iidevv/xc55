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

/**
 * Variants items list
 */
class QuickbooksSyncVariants extends \XC\ProductVariants\View\ItemsList\Model\ProductVariant
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = QuickbooksSyncData::TAB_VARIANTS;

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
        
        $list[] = 'modules/Qualiteam/SkinActQuickbooks/sync_data/variants.css';

        return $list;
    }

    /**
     * @inheritDoc
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();
        
        $newColumns = [];
        
        foreach ($columns as $columnName => $columnData) {
            
            if ($columnName != 'attributeValue') continue;
            
            $newColumns[$columnName] = $columnData;
        }
        
        $columns = $newColumns;
        
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
        $productId = $entity ? $entity->getProduct()->getProductId() : null;
        $variantId = $entity ? $entity->getId() : null;

        if ($productId && $variantId) {
            \XLite\Core\Database::getRepo(QuickbooksProducts::class)
                ->deleteVariant($productId, $variantId);
            
            return true;
        }

        return false;
    }
    
    /**
     * getEmptyListTemplate
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return $this->getEmptyListDir() . LC_DS . $this->getEmptyListFile();
    }
    
    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = parent::getFormParams();
        
        $params['product_id'] = $this->getProductId();

        return $params;
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
    
    /**
     * Mark list item as default
     *
     * @return boolean
     */
    protected function isDefault()
    {
        return false;
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return 'quickbooks_sync_variants';
    }
    
    /**
     * Get search form options
     *
     * @return array
     */
    public function getSearchFormOptions()
    {
        return [
            'target' => 'quickbooks_sync_variants',
        ];
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

        $result->{\XC\ProductVariants\Model\Repo\ProductVariant::SEARCH_QUICKBOOKS_VARIANTS} = true;

        return $result;
    }
    
    /**
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'data-widget' => 'Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncVariants'
        ];
    }
}