<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription as Subscription;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Base\Surcharge;
use XLite\Model\Order\Status\Payment;

/**
 * Class represents an order
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Order
{
    /**
     * Check if order has subscriptions
     *
     * @return boolean
     */
    public function hasSubscriptions()
    {
        $result = false;

        // TODO: This can be cheked by an SQL query instead
        foreach ($this->getItems() as $item) {
            if ($item->isSubscription()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Check if order has active subscriptions
     * 
     * @param bool $includePending Include not yet started subscriptions (from Queued orders) or not
     *
     * @return boolean
     */
    public function hasActiveSubscriptions($includePending = false)
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->getSubscription()) {

                $status = $item->getSubscription()->getStatus();
                if (
                    Base\ASubscriptionPlan::STATUS_ACTIVE == $status
                    || (
                        Base\ASubscriptionPlan::STATUS_NOT_STARTED == $status
                        && $includePending
                    )
                ) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * isSubscriptionPayment
     *
     * @return boolean
     */
    public function isSubscriptionPayment()
    {
        $isSubscriptionPayment = false;

        foreach ($this->getItems() as $item) {
            if ($item->getSubscription()
                && !$item->isInitialSubscription()
            ) {
                $isSubscriptionPayment = true;
            }
        }

        return $isSubscriptionPayment;
    }

    /**
     * getSubscription
     *
     * @return \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription
     */
    public function getSubscription()
    {
        $subscription = null;

        if ($this->isSubscriptionPayment()) {
            foreach ($this->getItems() as $item) {
                if ($item->isSubscription()
                    && !$item->isInitialSubscription()
                ) {
                    $subscription = $item->getSubscription();
                }
            }
        }

        return $subscription;
    }

    /**
     * Set payment status
     *
     * @param mixed $paymentStatus Payment status OPTIONAL
     *
     * @return void
     */
    public function setPaymentStatus($paymentStatus = null)
    {
        parent::setPaymentStatus($paymentStatus);

        if ($this->paymentStatus
            && Payment::STATUS_PAID == $this->paymentStatus->getCode()
        ) {
            $this->activateSubscriptions();
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processProcess()
    {
        parent::processProcess();

        $this->activateSubscriptions();
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processAuthorize()
    {
        parent::processAuthorize();

        $this->activateSubscriptions();
    }


    /**
     * Activate subscriptions in order
     *
     * @return void
     */
    protected function activateSubscriptions()
    {
        foreach ($this->getItems() as $item) {
            if ($item->isSubscription()
                && $item->getSubscription()
                && Base\ASubscriptionPlan::STATUS_NOT_STARTED == $item->getSubscription()->getStatus()
                && $item->getSubscription()->getInitialOrderItem()->getItemId() == $item->getItemId()
            ) {
                /** @var \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription $subscription */
                $subscription = $item->getSubscription();

                $successTries = $subscription->getSuccessTries() + 1;
                $subscription->setSuccessTries($successTries);

                $subscription->setStartDate(Converter::now());
                $nextDate = $subscription->getNextDate(Converter::now());

                $subscription->setPlannedDate($nextDate);
                $subscription->setRealDate($nextDate);

                $subscription->setStatus(Base\ASubscriptionPlan::STATUS_ACTIVE);

                $subscription->update();
            }
        }
    }

    /**
     * Save masked card id received on callback for subscriptions
     *
     * @param XpcTransactionData $card Masked card data
     *
     * @return void
     */
    public function setSavedCardForSubscriptions(XpcTransactionData $card)
    {
        foreach ($this->getItems() as $item) {
            if ($item->isSubscription()) {
                /** @var \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription $subscription */
                $subscription = $item->getSubscription();
                if ($subscription) {
                    $subscription->setXpcData($card);
                }
            }
        }
    }

    /**
     * Get shipping rates for order
     *
     * @return array
    */
    public function getShippingRates()
    {
        $rates = $this->getModifier(Surcharge::TYPE_SHIPPING, 'SHIPPING')->getRates();
        $result = [];
        foreach ($rates as $rate) {
            $result[] = [
                'method_id' => $rate->getMethodId(),
                'method_name' => $rate->getMethodName(),
                'total_rate' => $rate->getTotalRate(),
            ];
        }

        return $result;
    }

    /**
     * Get shipping address
     *
     * @return \XLite\Model\Address
    */
    public function getShippingAddress()
    {
        return $this->getProfile()->getShippingAddress();
    }
}
