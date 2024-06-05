<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BraintreeVZ\View;

use XCart\Extender\Mapping\Extender;

/**
 * Order history
 * @Extender\Mixin
 */
class OrderHistory extends \XLite\View\OrderHistory
{
    /**
     * Braintree tranaction
     */
    protected $braintreeTransaction = null;

    /**
     * Get Braintree transaction if any
     *
     * @return \XLite\Model\Payment\Transaction or null
     */
    protected function getBraintreeTransaction()
    {
        if (!$this->braintreeTransaction) {

            $transactions = $this->getOrder()->getPaymentTransactions();

            foreach ($transactions as $transaction) {
                if (
                    $transaction->isBraintreeProcessed()
                    && $transaction->isCompleted()
                    && $transaction->getBraintreeDataCell('bin')
                    && $transaction->getBraintreeDataCell('last4')
                    && $transaction->getBraintreeDataCell('cardType')
                    && $transaction->getBraintreeDataCell('imageUrl')
                ) {

                    $this->braintreeTransaction = $transaction;

                    break;
                }
            }

        }

        return $this->braintreeTransaction;
    }

    /**
     * Details getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return array
     */
    protected function getDetails(\XLite\Model\OrderHistoryEvents $event)
    {
        $list = parent::getDetails($event);

        if ($this->getBraintreeTransaction()) {
            foreach ($list as $columnId => $column) {
                foreach ($column as $cellId => $cell) {
                    if (in_array($cell->getName(), \QSL\BraintreeVZ\Model\Payment\Transaction::getDetailsExcludeKeys())) {
                        unset($list[$columnId][$cellId]);
                    }
                }
            }
        }

        return $list;
    }

}
