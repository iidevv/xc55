<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Controller\Admin;

use XLite\Controller\Features\SearchByFilterTrait;

class QuickbooksSyncProducts extends QuickbooksSyncData
{
    use SearchByFilterTrait;
    
    /**
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: 'Qualiteam\SkinActQuickbooks\View\ItemsList\QuickbooksSyncProducts';
    }

    // {{{ Search
    /**
     * Save search conditions
     */
    protected function doActionSearchItemsList()
    {
        // Clear stored filter within stored search conditions
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        parent::doActionSearchItemsList();

        $this->setReturnURL($this->getURL(['searched' => 1]));
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getSessionSearchConditions()
    {
        return parent::getSessionSearchConditions();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        return parent::getSearchParams();
    }
    // }}}
}