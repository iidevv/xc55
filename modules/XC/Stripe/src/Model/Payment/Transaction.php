<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model\Payment;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Payment\BackendTransaction;
use XC\Stripe\Main;

/**
 * Payment transaction
 * @Extender\Mixin
 */
class Transaction extends \XLite\Model\Payment\Transaction
{
    /**
     * Get charge value modifier
     *
     * @return float
     */
    public function getChargeValueModifier()
    {
        if (
            $this->isStripeConnect()
            && $this->type === BackendTransaction::TRAN_TYPE_GET_INFO
        ) {
            return 0;
        }

        return parent::getChargeValueModifier();
    }

    /**
     * @param $vendorId
     * @return int|mixed|null
     */
    public function getStripeTransferId($vendorId)
    {
        $transferId = null;
        $transactions = $this->getBackendTransactions();

        foreach ($transactions as $bt) {
            if ($bt->getType() !== BackendTransaction::TRAN_TYPE_SC_TRANSFER) {
                continue;
            }

            $btVendorId = $bt->getDataCell('vendor_id')
                ? $bt->getDataCell('vendor_id')->getValue()
                : null;

            if ($vendorId == $btVendorId) {
                $transferId = $bt->getDataCell('transfer_id')
                    ? $bt->getDataCell('transfer_id')->getValue()
                    : null;
            }
        }

        return $transferId;
    }

    /**
     * @return string|null
     */
    public function getStripeChargeId()
    {
        $value = null;
        foreach ($this->getData() as $cell) {
            if ($cell->getName() == 'sc_charge_id') {
                $value = $cell->getValue();
                break;
            }
        }
        return $value;
    }

    /**
     * @return bool
     */
    public function isStripeConnect()
    {
        return $this->getMethodName() === Main::STRIPE_CONNECT_SERVICE_NAME;
    }

    /**
     * @return bool
     */
    public function isStripe()
    {
        return $this->getMethodName() === Main::STRIPE_SERVICE_NAME;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        if (
            $this->isStripeConnect()
            && ($childOrderId = $this->getDetail('sc_order_id'))
        ) {
            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($childOrderId);

            if ($order) {
                return $order;
            }
        }

        return parent::getOrder();
    }
}
