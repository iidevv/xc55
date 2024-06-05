<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model\Payment\Processor;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Model\Payment\Transaction;

/**
 * X-Payments payment processor
 *
 * @Extender\Mixin
 */
class XPayments extends \Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\XPayments
{
    /**
     * Get input template
     *
     * @return string|void
     */
    public function getInputTemplate()
    {
        return
            (
                $this->getCart()
                && $this->getCart()->hasSubscriptions()
                && version_compare(Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version, '1.6') < 0
            )
            ? 'modules/Qualiteam/SkinActXPaymentsSubscriptions/checkout/save_card_box.twig'
            : parent::getInputTemplate();
    }

    /**
     * Process return
     *
     * @param Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(Transaction $transaction)
    {
        parent::processReturn($transaction);

        $order = $transaction->getOrder();
        if (
            $order
            && $order->hasSubscriptions()
            && !$order->isSubscriptionPayment()
            && $transaction->getXpcData()
        ) {
            // This is initial subscription order, set saved card id for it's subscriptions
            $order->setSavedCardForSubscriptions($transaction->getXpcData());
        }
    }
}
