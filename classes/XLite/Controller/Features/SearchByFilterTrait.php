<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Features;

/**
 * Trait search by filter
 */
trait SearchByFilterTrait
{
    /**
     * Clear search conditions used to reset saved filters
     */
    protected function doActionClearSearch()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }

    protected function doActionSearch()
    {
        // Clear stored search conditions
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        if (isset(\XLite\Core\Request::getInstance()->filter_id)) {
            return $this->doActionSearchByFilter();
        }

        parent::doActionSearchItemsList();
    }

    protected function doActionSearchByFilter()
    {
        $this->prepareSearchParams();

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }

    protected function prepareSearchParams()
    {
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = $this->getSearchFilterParams();
    }
}
