<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Inline\Select;

use XLite\View\FormField\Inline\Base\Single;

/**
 * Abstract order status
 */
class SubscriptionStatus extends Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return \Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Select\SubscriptionStatus::class;
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }
}
