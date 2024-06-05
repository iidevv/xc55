<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Module\XC\MultiVendor\Model\Payment;

use XCart\Extender\Mapping\Extender;
use XLite\InjectLoggerTrait;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;
use XC\MultiVendor\Main as MultiVendorMain;

/**
 * Stripe connect payment processor
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
abstract class StripeConnect extends \XC\Stripe\Model\Payment\StripeConnect
{
    use InjectLoggerTrait;

    /**
     * Get allowed backend transactions
     *
     * @return array Status codes
     */
    public function getAllowedTransactions()
    {
        $allowedTransactions = parent::getAllowedTransactions();

        if (MultiVendorMain::isWarehouseMode()) {
            $allowedTransactions = [
                \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            ];
        }

        return $allowedTransactions;
    }

    /**
     * @param Transaction $transaction
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createChildOrderTransactions(Transaction $transaction)
    {
        $order = $transaction->getOrder();

        $isCaptured = false;
        if ($chargeId = $transaction->getStripeChargeId()) {
            try {
                $charge     = \Stripe\Charge::retrieve($chargeId);
                $isCaptured = $charge->captured;
            } catch (\Exception $e) {
            }
        }

        /** @var \XLite\Model\Order $childOrder */
        foreach ($order->getChildren() as $index => $childOrder) {
            if (!MultiVendorMain::isWarehouseMode()) {
                $childTransaction = $transaction->cloneEntity();

                if ($childOrder->getVendor()) {
                    $childTransaction->setDataCell('vendor_id', $childOrder->getVendor()->getProfileId(), 'Vendor ID ', 'C');
                }

                $childTransaction->setPublicTxnId($transaction->getPublicTxnId() . '-' . $index);
                $childTransaction->setPublicId($transaction->getPublicId() . '-' . $index);
                $childTransaction->setDataCell('sc_order_id', $childOrder->getOrderId(), 'Order ID ', 'C');
                $childTransaction->setValue($childOrder->getTotal());
                $childTransaction->setStatus(Transaction::STATUS_SUCCESS);

                \XLite\Core\Database::getEM()->persist($childTransaction);
            } else {
                $childTransaction = $transaction;
            }

            if ($isCaptured) {
                $this->processVendorTransfer($childTransaction, $childOrder);
            }
        }

        if (!MultiVendorMain::isWarehouseMode()) {
            $transaction->setType(BackendTransaction::TRAN_TYPE_GET_INFO);
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * @param Transaction        $transaction
     * @param \XLite\Model\Order $childOrder
     *
     * @throws \Doctrine\ORM\ORMException
     */
    protected function processVendorTransfer($transaction, $childOrder)
    {
        $vendor = $childOrder->getVendor();

        if (!$vendor) {
            return;
        }

        try {
            if (!$vendor->getStripeSellerAccountId()) {
                throw new \Exception('Stripe account id not assigned for vendorId=' . $vendor->getProfileId());
            }

            $transferAmount = $childOrder->getCommission()
                ? $childOrder->getCommission()->getValue()
                : $childOrder->getSubtotal();

            $params = [
                'amount'             => $this->formatCurrency($transferAmount),
                'currency'           => $transaction->getCurrency()->getCode(),
                'destination'        => $vendor->getStripeSellerAccountId(),
                'source_transaction' => $transaction->getStripeChargeId(),
            ];

            $transfer = \Stripe\Transfer::create($params);

            $bt = $this->registerBackendTransaction(BackendTransaction::TRAN_TYPE_SC_TRANSFER, $transaction);
            $bt->setDataCell('vendor_id', $vendor->getProfileId());
            $bt->setDataCell('sc_order_id', $childOrder->getOrderId());

            $bt->setValue($transferAmount);
            $bt->setStatus(BackendTransaction::STATUS_SUCCESS);
            $bt->setDataCell('transfer_id', $transfer->id);
            $bt->setDataCell('transfer_amount', $transferAmount);

            if (!MultiVendorMain::isWarehouseMode()) {
                $transaction->setDataCell('transfer_id', $transfer->id, 'Transfer ID');
            }

            $bt->registerTransactionInOrderHistory();

            $childOrder->createProfileTransaction(
                $transferAmount,
                'Stripe Connect: Transfer created',
                \XC\MultiVendor\Model\ProfileTransaction::PROVIDER_STRIPE_CONNECT
            );
        } catch (\Exception $e) {
            $this->getLogger('XC-Stripe')->debug('Create transfer error: ' . $e->getMessage());
        }
    }

    /**
     * @param BackendTransaction $backendTransaction
     * @param                    $refundAmount
     */
    protected function registerVendorRefund(BackendTransaction $backendTransaction, $refundAmount)
    {
        $transaction = $backendTransaction->getPaymentTransaction();

        if (
            MultiVendorMain::isWarehouseMode()
            && $backendTransaction->getType() === BackendTransaction::TRAN_TYPE_REFUND
        ) {
            $this->registerWarehouseFullRefund($transaction);
        } else {
            $this->registerSeparateShopsVendorRefund($transaction, $refundAmount);
        }
    }

    /**
     * @param Transaction $transaction
     * @param             $refundAmount
     */
    protected function registerSeparateShopsVendorRefund(Transaction $transaction, $refundAmount)
    {
        $vendorId = $transaction->getDetail('vendor_id');

        $vendor = $vendorId
            ? \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($vendorId)
            : null;

        if (!$vendor) {
            return;
        }

        try {
            $reverseAmount = $this->calculateVendorRefundAmount($refundAmount, $vendor);

            $transaction->getOrder()->createProfileTransaction(
                $reverseAmount,
                'Order refunded or partially refunded'
            );

            $transferId = $transaction->getStripeTransferId($vendor->getProfileId());

            if (!$transferId) {
                throw new \Exception('Transfer id not found');
            }

            $this->registerTransferReversal($transaction, $transaction->getOrder(), $transferId, $reverseAmount);

            \XLite\Core\Database::getEM()->flush();
        } catch (\Exception $e) {
            $this->getLogger('XC-Stripe')->debug('Vendor reversal error: ' . $e->getMessage());
        }
    }

    /**
     * @param Transaction $transaction
     *
     * @throws \Exception
     */
    protected function registerWarehouseFullRefund(Transaction $transaction)
    {
        /** @var \XLite\Model\Order $childOrder */
        foreach ($transaction->getOrder()->getChildren() as $childOrder) {
            try {
                $vendor = $childOrder->getVendor();

                if (!$vendor) {
                    continue;
                }

                $commission = $childOrder->getCommission();
                if ($commission) {
                    $childOrder->createProfileTransaction(
                        $commission->getValue(),
                        'Order refunded'
                    );
                }

                $transferId = $transaction->getStripeTransferId($vendor->getProfileId());

                if (!$transferId) {
                    throw new \Exception('Transfer id not found');
                }

                $this->registerTransferReversal($transaction, $childOrder, $transferId);
            } catch (\Exception $e) {
                $this->getLogger('XC-Stripe')->debug('Child order refund error (order_id=' . $childOrder->getOrderId() . '): ' . $e->getMessage());
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * @param Transaction        $transaction
     * @param \XLite\Model\Order $order
     * @param                    $transferId
     * @param float|null         $reverseAmount
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function registerTransferReversal(Transaction $transaction, \XLite\Model\Order $order, $transferId, $reverseAmount = null)
    {
        $reversalParams = [];

        if ($reverseAmount) {
            $reversalParams['amount'] = $this->formatCurrency($reverseAmount);
        }

        $reversal = \Stripe\Transfer::createReversal($transferId, $reversalParams);

        $reverseAmount = $transaction->getCurrency()->convertIntegerToFloat($reversal->amount);
        $bt = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_SC_TRANSFER_REVERSE);
        $bt->setValue($reverseAmount);
        $bt->setStatus(BackendTransaction::STATUS_SUCCESS);
        $bt->setDataCell('reverse_id', $reversal->id);

        $bt->registerTransactionInOrderHistory();

        $order->createProfileTransaction(
            -$reverseAmount,
            'Stripe Connect: Reversing transfer',
            \XC\MultiVendor\Model\ProfileTransaction::PROVIDER_STRIPE_CONNECT
        );

        \XLite\Core\Database::getEM()->persist($bt);
    }

    /**
     * Process event charge.refunded
     *
     * @param \Stripe\Event $event       Event
     * @param Transaction   $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventChargeRefunded($event, $transaction)
    {
        $eventObj  = $event->data->object ?? null;
        $refundObj = $eventObj->refunds instanceof \Stripe\Collection
            ? $eventObj->refunds->first()
            : null;

        if (!$refundObj) {
            $this->getLogger('XC-Stripe')->debug('Refund object not found', [
                'function' => __FUNCTION__,
                'eventId'  => $event->id,
            ]);

            return;
        }

        if ($this->getSCBackendTransaction('sc_refund_id', $refundObj->id)) {
            $this->getLogger('XC-Stripe')->debug('Error: Event already processed', [
                'function' => __FUNCTION__,
                'eventId'  => $event->id,
            ]);

            return;
        }

        $refundAmount = $transaction->getCurrency()->convertIntegerToFloat($refundObj->amount);

        if (!MultiVendorMain::isWarehouseMode()) {
            $bt                 = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_GET_INFO);
            $orderHistorySuffix = 'Refund info';
        } else {
            $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND;

            if ($refundAmount != $transaction->getValue()) {
                $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART;
            }

            $bt                 = $transaction->createBackendTransaction($type);
            $orderHistorySuffix = 'callback';
        }

        $bt->setStatus(BackendTransaction::STATUS_SUCCESS);
        $bt->setValue($refundAmount);
        $bt->setDataCell('sc_refund_id', $refundObj->id);
        $bt->setDataCell('stripe_date', $refundObj->created);
        if ($refundObj->balance_transaction) {
            $bt->setDataCell('stripe_b_txntid', $refundObj->balance_transaction);
        }

        \XLite\Core\Database::getEM()->flush();

        $bt->registerTransactionInOrderHistory($orderHistorySuffix);
    }

    /**
     * Process event transfer.reversed
     *
     * @param \Stripe\Event $event       Event
     * @param Transaction   $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventTransferReversed($event, $transaction)
    {
        $eventObj    = $event->data->object ?? null;
        $reversalObj = $eventObj->reversals instanceof \Stripe\Collection
            ? $eventObj->reversals->first()
            : null;

        if (!$reversalObj) {
            $this->getLogger('XC-Stripe')->debug('Errro: Reversal object not found', [
                'function' => __FUNCTION__,
                'eventId'  => $event->id,
            ]);

            return;
        }

        if ($this->getSCBackendTransaction('reverse_id', $reversalObj->id)) {
            $this->getLogger('XC-Stripe')->debug('Error: Reverse already processed', [
                'function' => __FUNCTION__,
                'eventId'  => $event->id,
            ]);

            return;
        }

        $vendorOrder = null;
        if (MultiVendorMain::isWarehouseMode()) {
            $transferBt = $this->getSCBackendTransaction('transfer_id', $eventObj->id);
            if ($transferBt) {
                $orderId     = $transferBt->getDataCell('sc_order_id') ?
                    $transferBt->getDataCell('sc_order_id')->getValue()
                    : null;
                $vendorOrder = $orderId
                    ? \XLite\Core\Database::getRepo('XLite\Model\Order')->find($orderId)
                    : null;
            }
        } else {
            $vendorOrder = $transaction->getOrder();
        }

        $reverseAmount = $transaction->getCurrency()->convertIntegerToFloat($reversalObj->amount);
        $bt            = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_SC_TRANSFER_REVERSE);
        $bt->setStatus(BackendTransaction::STATUS_SUCCESS);
        $bt->setValue($reverseAmount);
        $bt->setDataCell('reverse_id', $reversalObj->id);

        \XLite\Core\Database::getEM()->persist($bt);
        \XLite\Core\Database::getEM()->flush();

        if ($vendorOrder) {
            $vendorOrder->createProfileTransaction(
                -$reverseAmount,
                'Stripe Connect: Reversing transfer',
                \XC\MultiVendor\Model\ProfileTransaction::PROVIDER_STRIPE_CONNECT
            );
        }

        $bt->registerTransactionInOrderHistory('Transfer callback');
    }

    /**
     * @param float                $refundAmount
     * @param \XLite\Model\Profile $vendor
     *
     * @return float|int
     */
    protected function calculateVendorRefundAmount(float $refundAmount, \XLite\Model\Profile $vendor)
    {
        return $refundAmount - ($refundAmount * $vendor->getRevshareFeeDst() / 100);
    }
}
