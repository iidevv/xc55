<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Create a profile for customers registered during checkout.
     */
    protected function saveAnonymousProfile()
    {
        parent::saveAnonymousProfile();

        // Reward history events are associated with the guest profile by now,
        // so we should update the records and associate with the correct
        // user profile (orig_profile).
        $this->moveRewardHistoryToOrigProfile();

        // Reward the customer for registering in the store
        LoyaltyProgram::getInstance()->rewardForSignup($this->getCart()->getOrigProfile());
    }

    /**
     * Merge anonymous profile
     */
    protected function mergeAnonymousProfile()
    {
        parent::mergeAnonymousProfile();

        // Reward history events are associated with the guest profile by now,
        // so we should update the records and associate with the correct
        // user profile (orig_profile).
        $this->moveRewardHistoryToOrigProfile();
    }

    /**
     * Moves reward history records from the guest profile to the orig_profile.
     */
    protected function moveRewardHistoryToOrigProfile()
    {
        $cart              = $this->getCart();
        $guestProfile      = $cart->getProfile();
        $registeredProfile = $cart->getOrigProfile();

        foreach ($guestProfile->getRewardEvents() as $event) {
            $event->setUser($registeredProfile);
        }

        $registeredProfile->addRewardPoints($guestProfile->getRewardPoints());
        $guestProfile->setRewardPoints(0);
    }

    /**
     * Restore order
     */
    protected function restoreOrder()
    {
        parent::restoreOrder();

        // Return applied reward points to the user if his order reverts back to the "cart" state
        $this->getCart()->processUnredeemPoints();
    }
}
