<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Cart coupons
 *
 * @ListChild (list="cart.panel.box", weight="195")
 * @ListChild (list="checkout.review.selected", weight="14")
 * @ListChild (list="checkout_fastlane.sections.details", weight="195")
 */
class RedeemPoints extends \XLite\View\AView
{
    /**
     * Get the maximum number of reward points a customer may apply on the order.
     *
     * @return integer
     */
    public function getApplicablePoints()
    {
        $cart    = $this->getCart();
        $profile = $cart ? $cart->getOrigProfile() : null;

        if ($profile) {
            // The number of points required for the maximum discount
            $requiredPoints = $cart->calculateRequiredRewardPoints();

            // The number of points the shopper wants to redeem for the order
            $userLimit = \XLite\Core\Request::getInstance()->redeemPoints;

            // Check whether the shopper wants to redeem all his points
            if (($userLimit !== (intval($userLimit) . '')) || (0 > $userLimit)) {
                $userLimit = $profile->getRewardPoints();
            }

            // The maximum number of points the shopper can redeem for the order
            $points = min(
                $profile->getRewardPoints(),
                $requiredPoints,
                $userLimit
            );
        } else {
            $points = 0;
        }

        return $points;
    }

    /**
     * Get the number of redeemed points.
     *
     * @return integer
     */
    public function getRedeemedPoints()
    {
        $cart = $this->getCart();

        $limit = $cart ? $cart->getMaxRedeemedRewardPoints() : -1;

        return (0 <= $limit)
            ? min($limit, $this->getApplicablePoints())
            : ($cart
                ? $cart->getRedeemedRewardPoints()
                : 0
            );
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'modules/QSL/LoyaltyProgram/redeem_points/styles.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/LoyaltyProgram/redeem_points/scripts.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/LoyaltyProgram/redeem_points/body.twig';
    }

    /**
     * Check if widget is visible.
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $cart    = $this->getCart();
        $profile = $cart ? $cart->getOrigProfile() : null;

        return $profile && ($profile->getRewardPoints() > 0);
    }
}
