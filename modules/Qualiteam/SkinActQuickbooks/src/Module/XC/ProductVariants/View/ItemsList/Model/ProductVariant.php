<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Module\XC\ProductVariants\View\ItemsList\Model;

use XLite\Core\Database;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts;
use XCart\Extender\Mapping\Extender;

/**
 * Product variants items list
 * 
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\View\ItemsList\Model\ProductVariant
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();
        
        $columns['quickbooks_fullname'] = [
            static::COLUMN_NAME      => static::t('QuickBooks Item Name/Number'),
            static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text',
            static::COLUMN_EDIT_ONLY => true,
            static::COLUMN_ORDERBY   => 310,
        ];
        
        return $columns;
    }
    
    /**
     * @inheritdoc
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        $product = $entity->getProduct() ?: $this->getProduct();
        
        Database::getRepo(QuickbooksProducts::class)
            ->deleteVariant(
                $product->getProductId(),
                $entity->getId()
            );

        return parent::removeEntity($entity);
    }
}