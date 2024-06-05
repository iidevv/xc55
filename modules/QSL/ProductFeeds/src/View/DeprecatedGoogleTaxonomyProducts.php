<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View;

/**
 * Widget displaying a list of products having a deprecated Google taxonomy category.
 */
class DeprecatedGoogleTaxonomyProducts extends \XLite\View\AView
{
    /**
     * Cached products having deprecated Google categories.
     *
     * @var array
     */
    protected $products;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/google.css';

        return $list;
    }

    /**
     * Get products having outdated Google categories.
     *
     * @return array
     */
    public function getProducts()
    {
        if (!isset($this->products)) {
            $this->products = $this->defineProducts();
        }

        return $this->products;
    }

    /**
     * Retrieve products having outdated Google categories.
     *
     * @return array
     */
    protected function defineProducts()
    {
        return $this->getProductRepo()->search($this->getProductQueryCondition());
    }

    /**
     * Count products having outdated Google categories.
     *
     * @return integer
     */
    protected function countProducts()
    {
        return $this->getProductRepo()->search($this->getProductQueryCondition(), true);
    }

    /**
     * Returns conditions to query products having outdated Google categories.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getProductQueryCondition()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Product::P_DEPRECATED_GOOGLE_TAXONOMY} = 1;

        return $cnd;
    }

    /**
     * Returns repository class for the Product model.
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getProductRepo()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/google.twig';
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/ProductFeeds/deprecated_products';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return 0 < $this->countProducts();
    }
}
