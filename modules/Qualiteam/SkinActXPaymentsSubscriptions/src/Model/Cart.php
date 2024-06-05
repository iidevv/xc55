<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Order\Status\Payment;

/**
 * Cart
 *
 * @Extender\Mixin
 */
abstract class Cart extends \XLite\Model\Cart
{
    /**
     * Check if order status is good to activate subscriptions
     *
     * @return bool
     */
    protected function isStatusToActivateSubscriptions()
    {
        return in_array(
            $this->getPaymentStatusCode(),
            [
                Payment::STATUS_PAID,
                Payment::STATUS_PART_PAID, // TODO: Consider this status
                Payment::STATUS_AUTHORIZED,
            ]
        );
    }

    /**
     * Init subscriptions
     *
     * @return void
     */
    public function initSubscriptions()
    {
        foreach ($this->getItems() as $item) {

            if (is_null($item->getSubscription()) && $item->isSubscription()) {

                $subscription = $item->getProduct()
                    ->getSubscriptionPlan()
                    ->createSubscription();
                $subscription->setFee($item->getDisplayFeePrice());
                $item->setSubscription($subscription);

                $subscription->setInitialOrderItem($item);

                if ($this->isStatusToActivateSubscriptions()) {

                    $successTries = $subscription->getSuccessTries() + 1;
                    $subscription->setSuccessTries($successTries);

                    $subscription->setStartDate(Converter::now());
                    $nextDate = $subscription->getNextDate(Converter::now());

                    $subscription->setPlannedDate($nextDate);
                    $subscription->setRealDate($nextDate);

                    $subscription->setStatus(
                        Base\ASubscriptionPlan::STATUS_ACTIVE
                    );

                    $subscription->registerEvent(
                        SubscriptionHistoryEvent::STATUS_SUCCESS
                    );

                } else {

                    $subscription->setStatus(
                        Base\ASubscriptionPlan::STATUS_NOT_STARTED
                    );

                    $subscription->registerEvent(
                        SubscriptionHistoryEvent::STATUS_FAILED
                    );
                }

                $item->update();
            }
        }
    }

    /**
     * Mark cart as order
     *
     * @return void
     */
    public function markAsOrder()
    {
        parent::markAsOrder();

        $this->initSubscriptions();
    }
}
