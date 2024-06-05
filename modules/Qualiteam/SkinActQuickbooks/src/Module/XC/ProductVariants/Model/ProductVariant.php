<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Module\XC\ProductVariants\Model;

use XLite\Core\Database;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts;
use XCart\Extender\Mapping\Extender;

/**
 * Product variant
 * 
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    /**
     * Get "QuickBooks Item Name/Number" value
     * 
     * @return string
     */
    public function getQuickbooksFullname()
    {
        $result = '';
        
        $product = $this->getProduct();
        
        if ($product) {
            $result = Database::getRepo(QuickbooksProducts::class)
                ->getQuickbooksFullname(
                    $product->getProductId(),
                    $this->getId()
                );
        }
        
        return $result;
    }
    
    /**
     * Set "QuickBooks Item Name/Number" value
     * 
     * @param string $value
     * 
     * @return ProductVariant
     */
    public function setQuickbooksFullname($value)
    {
        $product = $this->getProduct();
        
        if ($product) {
            Database::getRepo(QuickbooksProducts::class)
                ->setQuickbooksFullname(
                    $product->getProductId(),
                    $this->getId(),
                    $value
                );
        }
        
        return $this;
    }
}