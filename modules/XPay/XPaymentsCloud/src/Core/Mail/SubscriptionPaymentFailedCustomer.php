<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Core\Mail;

use XLite\Core\Converter;
use XLite\Model\Order;
use XLite\Core\Mailer;
use XPay\XPaymentsCloud\Model\Subscription\Subscription;

class SubscriptionPaymentFailedCustomer extends \XLite\Core\Mail\Order\ACustomer
{
    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'orderNumber' => '34',
                'actualDate'    => Converter::formatDate(Converter::time()),
                'pageUrl'     => Mailer::getXpaymentsSubscriptionsPageUrlWithTags(),
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionPaymentFailedCustomer constructor.
     *
     * @param Order        $order
     * @param Subscription $subscription
     */
    public function __construct(Order $order, Subscription $subscription)
    {
        parent::__construct($order);

        $this->populateVariables([
            'orderNumber' => $order->getOrderNumber(),
            'actualDate'    => \XLite\Core\Converter::formatDate($subscription->getActualDate()),
            'pageUrl'     => Mailer::getXpaymentsSubscriptionsPageUrlWithTags(),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return Mailer::XPAYMENTS_SUBSCRIPTION_PAYMENT_FAILED;
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
                \XLite\Core\OrderHistory::getInstance()->registerAdminEmailSent(
                    $order->getOrderId(),
                    static::t('Payment for subscription has failed')
                );
            } else {
                \XLite\Core\OrderHistory::getInstance()->registerAdminEmailFailed(
                    $order->getOrderId()
                );
            }
        }

        return $result;
    }

}
