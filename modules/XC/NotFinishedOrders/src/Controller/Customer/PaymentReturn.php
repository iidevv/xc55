<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Web-based payment method return
 * @Extender\Mixin
 */
abstract class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn
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
        parent::updateOrderState($txn);

        if (
            $txn->getOrder()
            && ($txn->getStatus() == \XLite\Model\Payment\Transaction::STATUS_FAILED
            || $txn->getStatus() == \XLite\Model\Payment\Transaction::STATUS_CANCELED)
        ) {
            $txn->getOrder()->setPaymentStatusByTransaction($txn);
        }
    }
}
