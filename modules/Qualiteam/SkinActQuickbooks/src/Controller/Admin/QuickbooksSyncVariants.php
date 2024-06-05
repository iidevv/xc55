<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XLite\Core\Request;
use XLite\Core\Database;
use XLite\Core\Session;
use XLite\Core\Converter;

class QuickbooksSyncVariants extends QuickbooksSyncData
{
    /**
     * doNoAction
     * 
     * @return void
     */
    public function doNoAction() {
        parent::doNoAction();

        if (
            !empty($_SERVER['HTTP_REFERER'])
            && strpos($_SERVER['HTTP_REFERER'], 'target=quickbooks_sync_products') !== false
        ) {
            Session::getInstance()->qsv_back_url = $_SERVER['HTTP_REFERER'];
        }
        
        if (!$this->getProductId() || !class_exists('XC\ProductVariants\Main')) {
            $this->redirect($this->getBackURL());
        }
    }
    /**
     * @return integer
     */
    public function getProductId()
    {
        return Request::getInstance()->product_id ?? 0;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        $name = '';
        
        if ($this->getProductId()) {
            $product = Database::getRepo('XLite\Model\Product')
                ->find($this->getProductId());
            if ($product) {
                $name = $product->getName();
            }
        }
        
        return $name;
    }
    
    /**
     * Alias
     *
     * @return \XLite\Model\Product|null
     */
    public function getProduct()
    {
        $product = Database::getRepo('XLite\Model\Product')
            ->find($this->getProductId());
        
        return $product;
    }
    
    /**
     * Get variants attributes
     *
     * @return array
     */
    public function getVariantsAttributes()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->getVariantsAttributes()->toArray();
        }
        
        return [];
    }
    
    /**
     * getBackURL
     * 
     * @return string
     */
    public function getBackURL()
    {
        if (Session::getInstance()->qsv_back_url) {
            return Session::getInstance()->qsv_back_url;
        } else {
            return Converter::buildFullURL(
                'quickbooks_sync_products',
                '',
                [],
                \XLite::getAdminScript()
            );
        }
    }
}