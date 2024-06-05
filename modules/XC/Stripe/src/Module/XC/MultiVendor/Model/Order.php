<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Module\XC\MultiVendor\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Order\Status\Payment;
use XLite\Model\Payment\BackendTransaction;

/**
 * Order model
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class Order extends \XLite\Model\Order
{
    protected bool $isChildrenStatusesHaveBeenSet = false;

    public function isChildrenStatusesHaveBeenSet(): bool
    {
        return $this->isChildrenStatusesHaveBeenSet;
    }

    public function setIsChildrenStatusesHaveBeenSet(bool $set): void
    {
        $this->isChildrenStatusesHaveBeenSet = $set;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection|\XLite\Model\Payment\Transaction[]
     */
    public function getPaymentTransactions()
    {
        $transactions = parent::getPaymentTransactions();

        if (
            !$this->isChild()
            || !$this->getOrderNumber()
        ) {
            return $transactions;
        }

        $stripeTransactions = $transactions->filter(function (\XLite\Model\Payment\Transaction $transaction) {
            if (!$transaction->isStripeConnect()) {
                return false;
            }
            $childOrderId = $transaction->getDetail('sc_order_id');

            return $childOrderId && $childOrderId == $this->getOrderId();
        });

        return count($stripeTransactions) > 0
            ? $stripeTransactions
            : $transactions;
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @param mixed  $oldStatus  Old order status
     * @param mixed  $newStatus  New order status
     * @param string $type Type
     *
     * @return string|array
     */
    protected function getStatusHandlers($oldStatus, $newStatus, $type)
    {
        $statusHandlers = parent::getStatusHandlers($oldStatus, $newStatus, $type);

        $oldCode = $oldStatus->getCode();
        $newCode = $newStatus->getCode();

        $isStripeConnect = false;
        foreach ($this->getPaymentTransactions() as $transaction) {
            if ($transaction->isStripeConnect()) {
                $isStripeConnect = true;
                break;
            }
        }

        if (
            $isStripeConnect
            && $type == 'payment'
            && $oldCode == Payment::STATUS_PAID
            && in_array($newCode, [Payment::STATUS_REFUNDED, Payment::STATUS_PART_PAID], true)
        ) {
            if (is_array($statusHandlers)) {
                $statusHandlers = array_filter($statusHandlers, static function ($callback) {
                    return $callback != 'createCredit';
                });
            } elseif ($statusHandlers === 'createCredit') {
                $statusHandlers = [];
            }
        }

        return $statusHandlers;
    }

    /**
     * Set order status by transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction which changes status
     *
     * @return void
     */
    public function setPaymentStatusByTransaction(\XLite\Model\Payment\Transaction $transaction)
    {
        if (
            $transaction->isStripeConnect()
            && $transaction->getType() === BackendTransaction::TRAN_TYPE_GET_INFO
        ) {
            return;
        } else {
            parent::setPaymentStatusByTransaction($transaction);
        }
    }

    /**
     * @param null $paymentStatus
     *
     * @return bool
     */
    protected function setMethodSpecificPaymentStatus($paymentStatus = null)
    {
        $parentResult = parent::setMethodSpecificPaymentStatus($paymentStatus);
        if ($parentResult) {
            return $parentResult;
        }

        $isStripeOrStripeConnect = false;
        foreach ($this->getPaymentTransactions() as $transaction) {
            if ($transaction->isStripeConnect() || $transaction->isStripe()) {
                $isStripeOrStripeConnect = true;
                break;
            }
        }

        if ($isStripeOrStripeConnect) {
            if (!$this->isChildrenStatusesHaveBeenSet()) {
                foreach ($this->getChildren() as $child) {
                    $child->setPaymentStatus(
                        $child->getCalculatedPaymentStatus(true)
                    );
                }
            }

            return true;
        }

        return false;
    }
}
