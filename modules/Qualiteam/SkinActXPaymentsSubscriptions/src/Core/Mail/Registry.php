<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core\Mail;

use XCart\Extender\Mapping\Extender;
use XLite;
use XLite\Core\Mailer;

/**
 * @Extender\Mixin
 */
class Registry extends \XLite\Core\Mail\Registry
{
    /**
     * @return array
     */
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            XLite::ZONE_CUSTOMER =>
                [
                    Mailer::SUBSCRIPTION_ORDER_CREATED       => OrderSubscriptionCreatedCustomer::class,
                    Mailer::SUBSCRIPTION_SUBSCRIPTION_FAILED => SubscriptionFailedCustomer::class,
                    Mailer::SUBSCRIPTION_PAYMENT_FAILED      => SubscriptionPaymentFailedCustomer::class,
                    Mailer::SUBSCRIPTION_PAYMENT_SUCCESSFUL  => SubscriptionPaymentSuccessfulCustomer::class,
                    Mailer::SUBSCRIPTION_STATUS_ACTIVE       => SubscriptionStatusCustomer::class,
                    Mailer::SUBSCRIPTION_STATUS_STOPPED      => SubscriptionStatusCustomer::class,
                ],
            XLite::ZONE_ADMIN    =>
                [
                    Mailer::SUBSCRIPTION_ORDER_CREATED       => OrderSubscriptionCreatedAdmin::class,
                    Mailer::SUBSCRIPTION_SUBSCRIPTION_FAILED => SubscriptionFailedAdmin::class,
                    Mailer::SUBSCRIPTION_PAYMENT_FAILED      => SubscriptionPaymentFailedAdmin::class,
                    Mailer::SUBSCRIPTION_PAYMENT_SUCCESSFUL  => SubscriptionPaymentSuccessfulAdmin::class,
                    Mailer::SUBSCRIPTION_STATUS_ACTIVE       => SubscriptionStatusAdmin::class,
                    Mailer::SUBSCRIPTION_STATUS_STOPPED      => SubscriptionStatusAdmin::class,
                ],
        ]);
    }
}
