<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Pager;

class Conversations extends \XLite\View\Pager\Customer\ACustomer
{
    /**
     * @inheritdoc
     */
    protected function getItemsPerPageMin()
    {
        return 10;
    }

    /**
     * isItemsPerPageVisible
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        return false;
    }
}
