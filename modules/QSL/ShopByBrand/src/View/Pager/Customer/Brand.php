<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Pager\Customer;

/**
 * Pager for the category products page
 */
class Brand extends \XLite\View\Pager\Customer\ACustomer
{
    /**
     * Return number of pages to display
     *
     * @return int
     */
    protected function getPagesPerFrame()
    {
        return 4;
    }

    /**
     * Return number of items per page
     *
     * @return int
     */
    protected function getItemsPerPageDefault()
    {
        return (int) \XLite\Core\Config::getInstance()->QSL->ShopByBrand->shop_by_brand_per_page;
    }

    /**
     * Return maximum possible items number per page (as many as defined by admin)
     *
     * @return int
     */
    protected function getItemsPerPageMax()
    {
        return $this->getItemsPerPageDefault() ?: parent::getItemsPerPageMax();
    }

    /**
     * getItemsPerPage
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        $current = $this->getItemsPerPageDefault();

        return max(
            min($this->getItemsPerPageMax(), $current),
            $this->getItemsPerPageMin()
        );
    }

    /**
     * Returns possible values for the "Brands per page" selector displayed by Crisp White theme.
     *
     * @return array
     */
    protected function getPerPageCounts()
    {
        return false;
    }

    /**
     * Should we use cache for pageId
     *
     * @return boolean
     */
    protected function isSavedPageId()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getPagerLabel()
    {
        return static::t('Brands');
    }
}
