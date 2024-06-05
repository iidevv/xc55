<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core\Mail;

use XLite\Model\Order;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionPlan;
use XLite\View\AView;
use XLite\Core\Converter;
use XLite\Core\Mail\Order\AAdmin;

class SubscriptionStatusAdmin extends AAdmin
{
    /**
     * @var Subscription $subscription
     */
    protected static $subscription;

    /**
     * @return array
     */
    protected static function defineVariables()
    {
        return [
                'subscriptionName' => 'My nice product',
                'subscriptionId'   => '67',
                'setupFee'         => AView::formatPrice(12),
                'subscriptionFee'  => AView::formatPrice(15),
                'planDescription'  => 'Every Monday',
                'plannedDate'      => Converter::formatDate(Converter::time()),
            ] + parent::defineVariables();
    }

    /**
     * SubscriptionStatusAdmin constructor.
     *
     * @param Subscription $subscription
     * @param Order $order
     */
    public function __construct(Subscription $subscription, Order $order)
    {
        parent::__construct($order);

        self::$subscription = $subscription;

        /** @var SubscriptionPlan $subscriptionPlan */
        $subscriptionPlan = $subscription->getProduct()->getSubscriptionPlan();

        $this->populateVariables([
            'subscriptionName' => $subscription->getProduct()->getName(),
            'subscriptionId'   => $subscription->getId(),
            'setupFee'         => AView::formatPrice($subscription->getInitialOrderItem()->getSetupFee()),
            'subscriptionFee'  => AView::formatPrice($subscription->getInitialOrderItem()->getDisplayFeePrice()),
            'planDescription'  => $subscriptionPlan->getPlanDescription(),
            'plannedDate'      => Converter::formatDate($subscription->getPlannedDate()),
        ]);
    }

    /**
     * Get directory
     *
     * @return string
     */
    public static function getDir()
    {
        return 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription_status_' . self::$subscription->getStatus();
    }
}
