<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\View\FormField\Inline\Select\OrderStatus;

use Qualiteam\SkinActShipStationAdvanced\View\FormField\Select\OrderStatus\Payment as PaymentInline;

class Payment extends \XLite\View\FormField\Inline\Select\OrderStatus\Payment
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return PaymentInline::class;
    }
}
