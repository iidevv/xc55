<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductComparison\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Product comparison widget
 *
 * @ListChild (list="sidebar.second", zone="customer", weight="400")
 */
class ProductComparison extends \XLite\View\SideBarBox
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/script.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return \XC\ProductComparison\Core\Data::getInstance()->getTitle();
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison/sidebar';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Is empty
     *
     * @return boolean
     */
    protected function isEmptyList()
    {
        return \XC\ProductComparison\Core\Data::getInstance()->getProductsCount() == 0;
    }

    /**
     * Get products
     *
     * @return array
     */
    protected function getProducts()
    {
        return \XC\ProductComparison\Core\Data::getInstance()->getProducts();
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-product-comparison';
    }
}
