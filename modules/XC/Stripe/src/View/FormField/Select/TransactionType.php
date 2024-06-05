<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\View\FormField\Select;

/**
 * ConnectAccount
 */
class TransactionType extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'sale' => 'Authorization and Capture',
            'auth' => 'Authorization only',
        ];
    }
}
