<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated controller for Edit Product page
 *
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    /**
     * Get list of tabs and associated targets.
     *
     * @return array
     */
    public function getPages()
    {
        $pages = parent::getPages();

        if (!$this->isNew()) {
            $pages += [
                'feed_categories_tab' => 'Feed categories',
            ];
        }

        return $pages;
    }

    /**
     * Get list of templates for tabs.
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $tpls = parent::getPageTemplates();

        if (!$this->isNew()) {
            $tpls += [
                'feed_categories_tab' => 'modules/QSL/ProductFeeds/product/feed_categories.twig',
            ];
        }

        return $tpls;
    }

    /**
     * Update feed categories selected for the product.
     *
     * @return void
     */
    protected function doActionUpdateFeedCategories()
    {
        $product = $this->getProduct();
        $product->map($this->getPostedData());

        \XLite\Core\Database::getEM()->flush();
    }
}
