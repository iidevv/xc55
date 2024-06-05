<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model\ProductSelection;

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
        return 5;
    }

    /**
     * Get items per page ranges list
     *
     * @return array
     */
    protected function getItemsPerPageRanges()
    {
        return [5, 10];
    }
}
