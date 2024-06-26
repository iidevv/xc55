<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XPay\XPaymentsCloud\Core\Mail as SubscriptionMail;
use XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XLite\Model\Order;

/**
 * Mailer core class
 *
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    const XPAYMENTS_SUBSCRIPTION_PATH_PREFIX         = 'modules/XPay/XPaymentsCloud/';
    const XPAYMENTS_SUBSCRIPTION_ORDER_CREATED       = self::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX . 'order_created';
    const XPAYMENTS_SUBSCRIPTION_SUBSCRIPTION_FAILED = self::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX . 'subscription_failed';
    const XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED      = self::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX . 'payment_failed';
    const XPAYMENTS_SUBSCRIPTION_PAYMENT_SUCCESSFUL  = self::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX . 'payment_successful';
    const XPAYMENTS_SUBSCRIPTION_STATUS_ACTIVE       = self::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX . 'subscription_status_A';
    const XPAYMENTS_SUBSCRIPTION_STATUS_STOPPED      = self::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX . 'subscription_status_S';

    /**
     * Returns Subscriptions page url
     *
     * @return string
     */
    public static function getXpaymentsSubscriptionsPageUrl()
    {
        $url = \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL(
                'xpayments_subscriptions',
                '',
                array(),
                \XLite::getCustomerScript()
            )
        );

        return $url;
    }

    /**
     * Return Subscriptions page url inside of <a></a> tag
     *
     * @return string
     */
    public static function getXpaymentsSubscriptionsPageUrlWithTags()
    {
        $url = static::getXpaymentsSubscriptionsPageUrl();

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
        if ($order->isXpaymentsSubscriptionPayment()) {
            $subscription = $order->getXpaymentsSubscription();
            static::sendXpaymentsOrderSubscriptionCreatedAdmin($order, $subscription);
            static::sendXpaymentsOrderSubscriptionCreatedCustomer($order, $subscription);

        } else {
            parent::sendOrderCreated($order);
        }
    }

    /**
     * Send created order mail to admin
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     *
     * @return void
     */
    public static function sendXpaymentsOrderSubscriptionCreatedAdmin(Order $order, Subscription $subscription)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\OrderSubscriptionCreatedAdmin::class,
                array($order, $subscription)
            )
        );
    }

    /**
     * Send created order mail to customer
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     *
     * @return void
     */
    public static function sendXpaymentsOrderSubscriptionCreatedCustomer(Order $order, Subscription $subscription)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\OrderSubscriptionCreatedCustomer::class,
                array($order, $subscription)
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
    public static function sendXpaymentsSubscriptionPaymentFailed(Order $order)
    {
        $subscription = $order->getXpaymentsSubscription();
        static::sendXpaymentsSubscriptionPaymentFailedAdmin($order, $subscription);
        static::sendXpaymentsSubscriptionPaymentFailedCustomer($order, $subscription);
    }

    /**
     * Send payment failed notification to admin
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionPaymentFailedAdmin(Order $order, Subscription $subscription)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentFailedAdmin::class,
                array($order, $subscription)
            )
        );
    }

    /**
     * Send payment failed notification to customer
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionPaymentFailedCustomer(Order $order, Subscription $subscription)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentFailedCustomer::class,
                array($order, $subscription)
            )
        );
    }

    /**
     * Send subscription failed notification
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     * @param string       $reason Reason for failing subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionFailed(Order $order, Subscription $subscription, string $reason = '')
    {
        static::sendXpaymentsSubscriptionFailedAdmin($order, $subscription, $reason);
        static::sendXpaymentsSubscriptionFailedCustomer($order, $subscription, $reason);
    }

    /**
     * Send subscription failed notification to admin
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     * @param string       $reason Reason for failing subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionFailedAdmin(Order $order, Subscription $subscription, string $reason)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionFailedAdmin::class,
                array($order, $subscription)
            )
        );
    }

    /**
     * Send subscription failed notification to customer
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     * @param string       $reason Reason for failing subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionFailedCustomer(Order $order, Subscription $subscription, string $reason)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionFailedCustomer::class,
                array($order, $subscription)
            )
        );
    }

    /**
     * Send payment successful notification
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionPaymentSuccessful(Order $order, Subscription $subscription)
    {
        static::sendXpaymentsSubscriptionPaymentSuccessfulAdmin($order, $subscription);
        static::sendXpaymentsSubscriptionPaymentSuccessfulCustomer($order, $subscription);
    }

    /**
     * Send payment successful notification to admin
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionPaymentSuccessfulAdmin(Order $order, Subscription $subscription)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentSuccessfulAdmin::class,
                array($order, $subscription)
            )
        );
    }

    /**
     * Send payment successful notification to customer
     *
     * @param Order        $order Order model
     * @param Subscription $subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionPaymentSuccessfulCustomer(Order $order, Subscription $subscription)
    {
        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionPaymentSuccessfulCustomer::class,
                array($order, $subscription)
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
    public static function sendXpaymentsSubscriptionStatus(Subscription $subscription)
    {
        $order = $subscription->getInitialOrder();

        static::sendXpaymentsSubscriptionStatusAdmin($order, $subscription);
        static::sendXpaymentsSubscriptionStatusCustomer($order, $subscription);
    }

    /**
     * Send subscription status change to admin
     *
     * @param Order $order Order
     * @param Subscription $subscription Subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionStatusAdmin(Order $order, Subscription $subscription)
    {
        new SubscriptionMail\SubscriptionStatusAdmin($order, $subscription);

        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionStatusAdmin::class,
                array($order, $subscription)
            )
        );
    }

    /**
     * Send subscription status change to customer
     *
     * @param Order $order Order
     * @param Subscription $subscription Subscription
     *
     * @return void
     */
    public static function sendXpaymentsSubscriptionStatusCustomer(Order $order, Subscription $subscription)
    {
        new SubscriptionMail\SubscriptionStatusCustomer($order, $subscription);

        static::getBus()->dispatch(
            new SendMail(
                SubscriptionMail\SubscriptionStatusCustomer::class,
                array($order, $subscription)
            )
        );
    }
}
