<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\ItemsList\Model;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XCart\Extender\Mapping\Extender;

/**
 * Order info
 *
 * @Extender\Mixin
 */
abstract class OrderItem extends \XLite\View\ItemsList\Model\OrderItem
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/subscription/style.css';

        return $list;
    }

    /**
     * Is next payment date available for current order
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isNextPaymentDateAvailable($item)
    {
        return $item->isNextPaymentDateAvailable();
    }

    /**
     * Is last payment failed for current subscription
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function isLastPaymentFailed($item)
    {
        $subscription = $item->getSubscription();

        return $subscription
            && $subscription->getRealDate() > $subscription->getPlannedDate();
    }

    /**
     * Get next payment date
     *
     * @param \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextPaymentDate($subscription)
    {
        return $subscription->getPlannedDate();
    }

    /**
     * Get next try date
     *
     * @param \Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription $subscription Subscription
     *
     * @return integer
     */
    protected function getNextTryDate($subscription)
    {
        return $subscription->getRealDate();
    }
}
