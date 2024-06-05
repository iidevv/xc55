<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Product modify
 *
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $pages = parent::getPages();
        if (!$this->isNew()) {
            $pages['upselling_products'] = static::t('Related products');
        }

        return $pages;
    }

    /**
     * The parent product ID definition
     *
     * @return string
     */
    public function getParentProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id ?: \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $tpls = parent::getPageTemplates();

        if (!$this->isNew()) {
            $tpls += [
                'upselling_products' => 'modules/XC/Upselling/upselling_products.twig',
            ];
        }

        return $tpls;
    }
}
