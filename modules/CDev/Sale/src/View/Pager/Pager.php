<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\Pager;

/**
 * Pager for Sale products list
 */
class Pager extends \XLite\View\Pager\APager
{
    /**
     * Return number of items per page
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 0;
    }

    /**
     * Return minimal possible items number per page
     *
     * @return integer
     */
    protected function getItemsPerPageMin(): int
    {
        return 0;
    }

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return 0;
    }

    /**
     * Hide "pages" part of widget
     *
     * @return boolean
     */
    protected function isPagesListVisible()
    {
        return false;
    }

    /**
     * Hide "items per page" part of widget
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        return false;
    }

    /**
     * Get pages count
     *
     * @return integer
     */
    public function getPagesCount()
    {
        return $this->executeCachedRuntime(function () {
            return ceil($this->getItemsTotal() / max(1, $this->getItemsPerPage()));
        });
    }
}
