<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core\Mail;

use XLite\Core\Converter;
use XLite\Core\Mail\Order\ACustomer;
use XLite\Core\OrderHistory;
use XLite\Model\Order;
use XLite\Core\Mailer;

class SubscriptionPaymentFailedCustomer extends ACustomer
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'orderNumber' => '34',
                'realDate'    => Converter::formatDate(Converter::time()),
                'pageUrl'     => Mailer::getSubscriptionsPageUrl(),
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionPaymentFailedCustomer constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        parent::__construct($order);

        $this->populateVariables([
            'orderNumber' => $order->getOrderNumber(),
            'realDate'    => Converter::formatDate($order->getSubscription()->getRealDate()),
            'pageUrl'     => Mailer::getSubscriptionsPageUrl(),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::SUBSCRIPTION_PAYMENT_FAILED;
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
                    'Payment for subscription has failed'
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
