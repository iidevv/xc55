<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Button\Customer;

/**
 * Add review button widget
 *
 */
class AddReviewLink extends \XC\Reviews\View\Button\Customer\AddReview
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/button/popup_link.twig';
    }

    /**
     * Return CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        return ' add-review ';
    }
}
