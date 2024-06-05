<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Pager\Customer;

/**
 * Abstract pager class for the Reviews list widget
 *
 */
abstract class ACustomer extends \XLite\View\Pager\Customer\ACustomer
{
    /**
     * Return number of items per page
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 10;
    }
}
