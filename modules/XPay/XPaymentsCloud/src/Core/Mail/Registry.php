<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * Mail registry
 *
 * @Extender\Mixin
 */
abstract class Registry extends \XLite\Core\Mail\Registry implements \XLite\Base\IDecorator
{
    /**
     * Get notifications list
     *
     * @return array
     */
    protected static function getNotificationsList()
    {
        return array_merge_recursive(
            parent::getNotificationsList(),
            array(
                \XLite::CUSTOMER_INTERFACE => array(
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_ORDER_CREATED       => OrderSubscriptionCreatedCustomer::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_SUBSCRIPTION_FAILED => SubscriptionFailedCustomer::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED      => SubscriptionPaymentFailedCustomer::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_SUCCESSFUL  => SubscriptionPaymentSuccessfulCustomer::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_ACTIVE       => SubscriptionStatusCustomer::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_STOPPED      => SubscriptionStatusCustomer::class,
                ),
                \XLite::ADMIN_INTERFACE => array(
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_ORDER_CREATED       => OrderSubscriptionCreatedAdmin::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_SUBSCRIPTION_FAILED => SubscriptionFailedAdmin::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED      => SubscriptionPaymentFailedAdmin::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_SUCCESSFUL  => SubscriptionPaymentSuccessfulAdmin::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_ACTIVE       => SubscriptionStatusAdmin::class,
                    \XLite\Core\Mailer::XPAYMENTS_SUBSCRIPTION_STATUS_STOPPED      => SubscriptionStatusAdmin::class,
                ),
            )
        );
    }
}
