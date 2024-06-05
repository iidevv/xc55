<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core\Mail;

use XLite\Core\Mail\Order\AAdmin;
use XLite\Core\OrderHistory;
use XLite\Model\Order;
use XLite\Core\Mailer;

class SubscriptionFailedAdmin extends AAdmin
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionId'       => '67',
                'reason'          => 'Shipping method is unavailable',
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionFailedAdmin constructor.
     *
     * @param Order $order Order model
     * @param string $reason Reason for failing subscription
     */
    public function __construct(Order $order, string $reason = '')
    {
        parent::__construct($order);

        $this->populateVariables([
            'subscriptionId' => $order->getSubscription()->getId(),
            'reason'         => $reason
                ? '<p>Reason: ' . $reason . '</p>'
                : '',
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::SUBSCRIPTION_SUBSCRIPTION_FAILED;
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
                    'Subscription has failed'
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
