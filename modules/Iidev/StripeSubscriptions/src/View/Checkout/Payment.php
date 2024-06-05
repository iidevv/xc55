<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Iidev\StripeSubscriptions\View\Checkout;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout payment
 * @Extender\Mixin
 */
abstract class Payment extends \XLite\View\Checkout\Payment
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Iidev/StripeSubscriptions/checkout/style.css';

        return $list;
    }

}
