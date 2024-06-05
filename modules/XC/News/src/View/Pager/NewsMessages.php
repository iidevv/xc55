<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\View\Pager;

/**
 * Abstract pager class for the NewsMessages widget
 */
class NewsMessages extends \XLite\View\Pager\Customer\ACustomer
{
    /**
     * Return number of items per page
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return (int) \XLite\Core\Config::getInstance()->XC->News->items_per_page;
    }

    /**
     * getPagerLabel
     *
     * @return label
     */
    protected function getPagerLabel()
    {
        return static::t('News');
    }
}
