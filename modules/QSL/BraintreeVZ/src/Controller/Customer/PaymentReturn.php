<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BraintreeVZ\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Return to the store after payment
 * @Extender\Mixin
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn
{

    /**
     * Updates order state by transaction
     *
     * @param \XLite\Model\Payment\Transaction $txn Processed payment transaction
     *
     * @return void
     */
    public function updateOrderState($txn)
    {
        if (!$txn->isBraintreeTransaction()) {
            parent::updateOrderState($txn);
        }
    }

}