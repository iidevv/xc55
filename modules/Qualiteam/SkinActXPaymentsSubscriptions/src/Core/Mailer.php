<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core;

use Qualiteam\SkinActXPaymentsSubscriptions\Core\Mail as SubscriptionMail;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XLite;
use XLite\Model\Order;

/**
 * Mailer core class
 *
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    const SUBSCRIPTION_PATH_PREFIX         = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/';
    const SUBSCRIPTION_ORDER_CREATED       = self::SUBSCRIPTION_PATH_PREFIX . 'order_created';
    const SUBSCRIPTION_SUBSCRIPTION_FAILED = self::SUBSCRIPTION_PATH_PREFIX . 'subscription_failed';
    const SUBSCRIPTION_PAYMENT_FAILED      = self::SUBSCRIPTION_PATH_PREFIX . 'payment_failed';
    const SUBSCRIPTION_PAYMENT_SUCCESSFUL  = self::SUBSCRIPTION_PATH_PREFIX . 'payment_successful';
    const SUBSCRIPTION_STATUS_ACTIVE       = self::SUBSCRIPTION_PATH_PREFIX . 'subscription_status_A';
    const SUBSCRIPTION_STATUS_STOPPED      = self::SUBSCRIPTION_PATH_PREFIX . 'subscription_status_S';

    /**
     * Returns Subscriptions page url
     *
     * @return string
     */
    public static function getSubscriptionsPageUrl()
    {
        $url = XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL(
                'x_payments_subscription',
                '',
                [],
                XLite::getCustomerScript()
            )
        );

        return sprintf('<a href="%s">%s</a>', $url, $url);
    }

    /**
     * Send created order mails.
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendOrderCreated(Order $order)
    {
        if ($order->isSubscriptionPayment()) {
            static::sendOrderSubscriptionCreatedAdmin($order);
            static::sendOrderSubscriptionCreatedCustomer($order);

        } else {
            parent::sendOrderCreated($order);
        }
    }

    /**
     * Send created order mail to admin
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendOrderSubscriptionCreatedAdmin(Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\OrderSubscriptionCreatedAdmin::class,
                [$order]
            )
        );
    }

    /**
     * Send created order mail to customer
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendOrderSubscriptionCreatedCustomer(Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\OrderSubscriptionCreatedCustomer::class,
                [$order]
            )
        );
    }


    /**
     * Send payment failed notification.
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendSubscriptionPaymentFailed(Order $order)
    {
        static::sendSubscriptionPaymentFailedAdmin($order);
        static::sendSubscriptionPaymentFailedCustomer($order);
    }

    /**
     * Send payment failed notification to admin
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendSubscriptionPaymentFailedAdmin(Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentFailedAdmin::class,
                [$order]
            )
        );
    }

    /**
     * Send payment failed notification to customer
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendSubscriptionPaymentFailedCustomer(Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentFailedCustomer::class,
                [$order]
            )
        );
    }

    /**
     * Send subscription failed notification
     *
     * @param Order $order Order model
     * @param string $reason Reason for failing subscription
     *
     * @return void
     */
    public static function sendSubscriptionFailed(Order $order, string $reason = '')
    {
        static::sendSubscriptionFailedAdmin($order, $reason);
        static::sendSubscriptionFailedCustomer($order, $reason);
    }

    /**
     * Send subscription failed notification to admin
     *
     * @param Order $order Order model
     * @param string $reason Reason for failing subscription
     *
     * @return void
     */
    public static function sendSubscriptionFailedAdmin(Order $order, string $reason)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionFailedAdmin::class,
                [$order, $reason]
            )
        );
    }

    /**
     * Send subscription failed notification to customer
     *
     * @param Order $order Order model
     * @param string $reason Reason for failing subscription
     *
     * @return void
     */
    public static function sendSubscriptionFailedCustomer(Order $order, string $reason)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionFailedCustomer::class,
                [$order, $reason]
            )
        );
    }

    /**
     * Send payment successful notification
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendSubscriptionPaymentSuccessful(Order $order)
    {
        static::sendSubscriptionPaymentSuccessfulAdmin($order);
        static::sendSubscriptionPaymentSuccessfulCustomer($order);
    }

    /**
     * Send payment successful notification to admin
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendSubscriptionPaymentSuccessfulAdmin(Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentSuccessfulAdmin::class,
                [$order]
            )
        );
    }

    /**
     * Send payment successful notification to customer
     *
     * @param Order $order Order model
     *
     * @return void
     */
    public static function sendSubscriptionPaymentSuccessfulCustomer(Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentSuccessfulCustomer::class,
                [$order]
            )
        );
    }

    /**
     * Send subscription status details notification
     *
     * @param Subscription $subscription Subscription
     *
     * @return void
     */
    public static function sendSubscriptionStatus(Subscription $subscription)
    {
        $order = $subscription->getInitialOrder();

        static::sendSubscriptionStatusAdmin($subscription, $order);
        static::sendSubscriptionStatusCustomer($subscription, $order);
    }

    /**
     * Send subscription status change to admin
     *
     * @param Subscription $subscription Subscription
     * @param Order $order Order
     *
     * @return void
     */
    public static function sendSubscriptionStatusAdmin(Subscription $subscription, Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionStatusAdmin::class,
                [$subscription, $order]
            )
        );
    }

    /**
     * Send subscription status change to customer
     *
     * @param Subscription $subscription Subscription
     * @param Order $order Order
     *
     * @return void
     */
    public static function sendSubscriptionStatusCustomer(Subscription $subscription, Order $order)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionStatusCustomer::class,
                [$subscription, $order]
            )
        );
    }
}
