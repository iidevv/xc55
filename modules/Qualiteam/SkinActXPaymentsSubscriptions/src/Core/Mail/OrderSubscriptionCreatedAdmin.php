<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core\Mail;

use XLite\Core\Mail\Order\AAdmin;
use XLite\Core\OrderHistory;
use XLite\Model\Order;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use XLite\Core\Converter;
use XLite\Core\Mailer;

/**
 * OrderSubscriptionCreatedAdmin
 */
class OrderSubscriptionCreatedAdmin extends AAdmin
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionId'       => '67',
                'orderNumber'          => '42',
                'pendingPaymentNumber' => '7 payment total of 12',
                'realDate'             => Converter::formatDate(Converter::time()),
            ] + parent::defineVariables();
    }

    /**
     * OrderSubscriptionCreatedAdmin constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        parent::__construct($order);

        /** @var Subscription $subscription */
        $subscription = $order->getSubscription();

        $this->populateVariables([
            'subscriptionId'       => $subscription->getId(),
            'orderNumber'          => $order->getOrderNumber(),
            'pendingPaymentNumber' => $subscription->getPeriods()
                ? '<br>' . $subscription->getPendingPaymentNumber() . ' payment total of ' . $subscription->getPeriods()
                : '',
            'realDate'             => Converter::formatDate($subscription->getRealDate()),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::SUBSCRIPTION_ORDER_CREATED;
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
                    'Order for subscription is initially created'
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
