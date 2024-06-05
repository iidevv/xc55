<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View;

use Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionPlan;
use XCart\Extender\Mapping\Extender;

/**
 * Product price
 *
 * @Extender\Mixin
 */
abstract class Price extends \XLite\View\Price
{
    /**
     * Check if is need to show subscriptions info
     *
     * @return boolean
     */
    protected function isShowSubscriptionInfo()
    {
        return $this->hasSubscription();
    }

    /**
     * Check if product has subscription plan
     *
     * @return boolean
     */
    protected function hasSubscription()
    {
        return $this->getProduct()
            && $this->getProduct()->hasSubscriptionPlan();
    }

    /**
     * getSubscriptionPlan
     *
     * @return SubscriptionPlan
     */
    protected function getSubscriptionPlan()
    {
        return $this->hasSubscription()
            ? $this->getProduct()->getSubscriptionPlan()
            : null;
    }

    /**
     * Get calculated Setup fee
     *
     * @return float|integer
     */
    protected function getSetupFee()
    {
        $fee = $this->hasSubscription()
            ? $this->getProduct()->getDisplayFeePrice()
            : 0;

        return $this->getListPrice() - $fee;
    }

    /**
     * getPlanDescription
     *
     * @return string
     */
    protected function getPlanDescription()
    {
        return $this->hasSubscription()
            ? $this->getSubscriptionPlan()->getLongDescription()
            : '';
    }

    /**
     * getPlanDescription
     *
     * @return string
     */
    protected function getTotalPaymentsDescription()
    {
        return $this->hasSubscription()
            ? $this->getSubscriptionPlan()->getTotalPaymentsDescription()
            : '';
    }
}
