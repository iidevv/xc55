<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Customer;

/**
 * ACustomer
 */
abstract class ACustomer extends \XLite\View\Pager\APager
{
    /**
     * Get items per page default
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 10;
    }

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return \XLite\Core\Request::isMobileDevice() ? 2 : 4;
    }
}
