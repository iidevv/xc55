<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model\PaymentMethod;

/**
 * Table-based pager
 */
class Table extends \XLite\View\Pager\Admin\Model\Table
{
    /**
     * Get items per page (default)
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 15;
    }

    /**
     * Get items per page ranges list
     *
     * @return array
     */
    protected function getItemsPerPageRanges()
    {
        return [10, 15, 20];
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
}
