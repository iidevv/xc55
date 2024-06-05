<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core\Mail;

use XLite\Core\Converter;
use XLite\Core\Mail\Order\AAdmin;
use XLite\Core\OrderHistory;
use XLite\Model\Order;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use XLite\Core\Mailer;

class SubscriptionPaymentSuccessfulAdmin extends AAdmin
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionId' => '67',
                'plannedDate'    => Converter::formatDate(Converter::time()),
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionPaymentSuccessfulAdmin constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        parent::__construct($order);

        /** @var Subscription $subscription */
        $subscription = $order->getSubscription();

        $this->populateVariables([
            'subscriptionId' => $subscription->getId(),
            'plannedDate'    => Converter::formatDate($subscription->getPlannedDate()),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::SUBSCRIPTION_PAYMENT_SUCCESSFUL;
    }

    /**
     * @return bool
     */
    public function send()
    {
        $result = parent::send();

        $order = $this->getOrder();

        if ($order) {
            if ($result) {
                OrderHistory::getInstance()->registerAdminEmailSent(
                    $order->getOrderId(),
                    'Payment for subscription is successful'
                );
            } else {
                OrderHistory::getInstance()->registerAdminEmailFailed(
                    $order->getOrderId()
                );
            }
        }

        return $result;
    }

}
