<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select\OrderStatus;

/**
 * Shipping order status
 */
class Shipping extends \XLite\View\FormField\Inline\Select\OrderStatus\AOrderStatus
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Select\OrderStatus\Shipping';
    }
}
