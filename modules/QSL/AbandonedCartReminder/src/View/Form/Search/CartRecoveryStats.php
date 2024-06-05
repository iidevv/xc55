<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Form\Search;

/**
 * Filter Cart Recovery Statistics form class.
 */
class CartRecoveryStats extends \XLite\View\Form\AForm
{
    /**
     * Return default target for the search form.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'cart_recovery_stats';
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
