<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Customer;

use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * Controller for the Reward Points tab in user profiles.
 */
class RewardPoints extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->widget_title ?: static::t('My account');
    }

    /**
     * Check if current page is accessible.
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Define current location for breadcrumbs.
     *
     * @return string
     */
    protected function getLocation()
    {
        return 'Reward points';
    }

    /**
     * Redeem points.
     */
    protected function doActionRedeem()
    {
        $cart    = $this->getCart();
        $profile = $cart ? $cart->getOrigProfile() : null;

        if ($profile) {
            $loyaltyProgram = LoyaltyProgram::getInstance();

            // The maximum discount the shopper may ever get for the order
            $maxDiscount = $loyaltyProgram->calculateMaximumRewardDiscount($cart);

            // The number of points required for the maximum discount
            $requiredPoints = $loyaltyProgram->calculatePointsToRedeem($maxDiscount);

            // The number of points the shopper wants to redeem for the order
            $userLimit = \XLite\Core\Request::getInstance()->redeemPoints;

            // Check whether the shopper wants to redeem all his points
            if (($userLimit !== (intval($userLimit) . '')) || (0 > $userLimit)) {
                $userLimit = $profile->getRewardPoints();
            }

            // The number of points the shopper will redeem actually
            $points = min(
                $profile->getRewardPoints(),
                $requiredPoints,
                $userLimit
            );
        } else {
            $points = 0;
        }

        // Do not update if it the same number the shopper have redeemed already
        if ($cart->getMaxRedeemedRewardPoints() != $points) {
            $cart->setMaxRedeemedRewardPoints($points);

            $this->updateCart();
            \XLite\Core\Database::getEM()->flush();
            \XLite\Core\TopMessage::addInfo('You have changed the number of reward points applied on the order.');
        }

        $this->setPureAction();
    }

    /**
     * Controller marks the cart calculation.
     * In some cases we do not need to recalculate the cart.
     * We need it mainly on the checkout page.
     *
     * @return boolean
     */
    protected function markCartCalculate()
    {
        return true;
    }
}
