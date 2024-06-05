<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Form\Search;

/**
 * Filter Cart Email Statistics form class.
 */
class CartEmailStats extends \XLite\View\Form\AForm
{
    /**
     * Return default target for the search form.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'cart_email_stats';
    }

    /**
     * Return default action for the search form.
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'search_items_list';
    }
}
