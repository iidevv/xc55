<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\Form\Checkout;

/**
 * Post office selection
 */
class PostOffice extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'capost_office';
    }
}
