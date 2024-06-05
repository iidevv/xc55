<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\Stripe\Module\XC\MultiVendor\Model\Payment;

use XLite\Model\Order;
use XLite\Model\Payment\Transaction;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 * @Extender\After("XC\Stripe")
 */
class Stripe extends \XC\Stripe\Model\Payment\Stripe
{
    protected function maybeSetChildrenPaymentStatuses(Transaction $transaction, string $oldTransactionStatus): void
    {
        $order = $transaction->getOrder();

        if ($transaction->getStatus() !== $oldTransactionStatus) {
            $order->setIsChildrenStatusesHaveBeenSet(true);
            /** @var Order $child */
            foreach ($order->getChildren() as $child) {
                $child->setPaymentStatusByTransaction($transaction);
            }
        }
    }

    protected function successTransaction($event, Transaction $transaction): void
    {
        $oldTransactionStatus = $transaction->getStatus();

        parent::successTransaction($event, $transaction);

        $this->maybeSetChildrenPaymentStatuses($transaction, $oldTransactionStatus);
    }

    protected function failTransaction($event, Transaction $transaction): void
    {
        $oldTransactionStatus = $transaction->getStatus();

        parent::failTransaction($event, $transaction);

        $this->maybeSetChildrenPaymentStatuses($transaction, $oldTransactionStatus);
    }
}
