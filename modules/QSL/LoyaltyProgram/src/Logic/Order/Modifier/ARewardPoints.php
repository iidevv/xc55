<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic\Order\Modifier;

use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * Discount for reward points the shopper spend on the order.
 */
abstract class ARewardPoints extends \XLite\Logic\Order\Modifier\Discount
{
    /**
     * Modifier type (see \XLite\Model\Base\Surcharge)
     *
     * @var string
     */
    protected $type = \XLite\Model\Base\Surcharge::TYPE_DISCOUNT;

    /**
     * Cached number of reward points to display for the discount in cart totals.
     *
     * @var integer
     */
    protected $redeemed = 0;

    /**
     * Cached value of the discount.
     *
     * @var float
     */
    protected $discount;

    /**
     * Whether the name of the surcharge should be short, or should include
     * the number of points.
     *
     * We use the short name when adding the surcharge to the database (as
     * there is no way to alter it in the database currently), but the full
     * name when displaying it on the site.
     *
     * @var bool
     */
    protected $shortSurchargeInfo = false;

    /**
     * Check - can apply this modifier or not.
     *
     * @return boolean
     */
    public function canApply()
    {
        return parent::canApply()
            && $this->isTimeToApply()
            && $this->hasDiscount();
    }

    /**
     * Calculate and return added surcharge or array of surcharges
     *
     * @return \XLite\Model\Order\Surcharge|array
     */
    public function calculate()
    {
        $discount = $this->getDiscount();

        $this->shortSurchargeInfo = true;
        $surcharge                = $this->addOrderSurcharge($this->getCode(), $discount * -1, false);
        $this->shortSurchargeInfo = false;

        // Distribute discount value among the ordered products
        $this->distributeDiscount($discount);

        return $surcharge;
    }

    /**
     * Get surcharge name.
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    public function getSurchargeInfo(\XLite\Model\Base\Surcharge $surcharge)
    {
        $info       = new \XLite\DataSet\Transport\Order\Surcharge();
        $info->name = $this->getSurchargeInfoName($surcharge);

        return $info;
    }

    /**
     * Get surcharge title.
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return string
     */
    protected function getSurchargeInfoName(\XLite\Model\Base\Surcharge $surcharge)
    {
        return $this->shortSurchargeInfo
            ? \XLite\Core\Translation::lbl('Discount (reward points)')
            : \XLite\Core\Translation::lbl(
                'Discount ({{pts}} points)',
                [
                    'pts' => $this->getSurchargeInfoNumberOfPoints($surcharge),
                ]
            );
    }

    /**
     * Returns the number of redeemed reward points.
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return integer
     */
    protected function getSurchargeInfoNumberOfPoints(\XLite\Model\Base\Surcharge $surcharge)
    {
        // The number of points is stored in different places depending on whether it is a cart or an order
        return $surcharge->getOrder()
            ? $surcharge->getOrder()->getRedeemedRewardPoints()
            : $this->redeemed;
    }

    /**
     * Check for suitable discount
     *
     * @return boolean
     */
    protected function hasDiscount()
    {
        // getDiscount() returns a cached value that we store there in order
        // to get the same discount amount used in hasDiscount() and calculate()
        // methods. But since this object may be used more than once, we should
        // reset the cache before the initial check.
        $this->resetCachedDiscount();

        return $this->getDiscount() > 0;
    }

    /**
     * Get the discount amount.
     *
     * @return float
     */
    protected function getDiscount()
    {
        if (!isset($this->discount)) {
            $this->discount = $this->calculateDiscount();
        }

        return $this->discount;
    }

    /**
     * Deletes the cached value of the discount.
     */
    protected function resetCachedDiscount()
    {
        unset($this->discount);
    }

    /**
     * Calculate the discount amount.
     *
     * @return float
     */
    protected function calculateDiscount()
    {
        $loyaltyProgram = LoyaltyProgram::getInstance();

        $order   = $this->getOrder();
        $profile = $order ? $order->getOrigProfile() : null;

        $this->redeemed = 0;

        if ($profile) {
            // The number of points required for the maximum discount
            $requiredPoints = $order->calculateRequiredRewardPoints($this->getApplyMode());

            // The number of points the shopper wants to redeem for the order
            $userLimit = $order->getMaxRedeemedRewardPoints();

            // The number of points that the user has
            $userRewardPoints = max(0, $this->getFreeUserPoints($profile, $order));

            // The number of points that the user can use for the order
            $limit = min($userRewardPoints, (0 > $userLimit) ? $userRewardPoints : $userLimit);

            // The number of points that the user can use for the order
            // minus points that were used for the order already
            $unused = max(0, $limit - $order->getNumberOfUsedRewardPoints());

            // The maximum number of points that the shopper can redeem for the order
            $points = min(
                $requiredPoints,
                $unused
            );

            // Whether points for the order were redeemed from the user account in past
            $isRedeemedAlready = $order->getPointsRedeemed();
            $redeemedAlready   = $order->getRedeemedRewardPoints();

            // Do not apply more points than were used for placed orders already
            if (!($order instanceof \XLite\Model\Cart) && ($points > $redeemedAlready)) {
                $points = $redeemedAlready;
            }

            $redeemedPointsChanged = $isRedeemedAlready && (intval($points) <> intval($redeemedAlready));
            if ($redeemedPointsChanged) {
                // BT#0046209: X-Payments Subscriptions re-calculates an order
                // that was placed earlier. So, we should check if the number of
                // points has changed and revert the previous operation in this
                // case
                $order->processUnredeemPoints();
            }

            // Update the order to reflect the number of points the shopper redeems
            $order->setRedeemedRewardPoints($points);
            $order->addUsedRewardPoints($points);
            $this->redeemed = $points;

            if ($redeemedPointsChanged) {
                // BT#0046209: X-Payments Subscriptions re-calculates an order
                // that was placed earlier. So, we should check if the number of
                // points has changed and add redeem the new number of points
                // in this case
                $order->processRedeemPoints();
            }

            // The discount that the shopper get for the order actually
            $discount = min(
                $loyaltyProgram->calculateMaximumRewardDiscount($order, $this->getApplyMode()),
                $loyaltyProgram->calculateRedeemDiscount($points)
            );
        } else {
            $discount = 0;
        }

        return $discount;
    }

    /**
     * Returns the number of available reward points that a user can use for the order.
     *
     * @param \XLite\Model\Profile $profile User profile
     * @param \XLite\Model\Order   $order   Order
     *
     * @return integer
     */
    protected function getFreeUserPoints(\XLite\Model\Profile $profile, \XLite\Model\Order $order)
    {
        $points     = $profile->getRewardPoints();
        $isRedeemed = $order->getPointsRedeemed();
        $redeemed   = $order->getRedeemedRewardPoints();

        if (!($this instanceof \XLite\Model\Cart) || $this->canRedeemedPointsChange()) {
            // BT#0046209: X-Payments Subscriptions re-calculates an order
            // that was placed earlier. So, we should consider the points
            // that were applied on the order already as "free" too.
            // The same should happen when we are editing an existing order.
            $points += ($isRedeemed ? $redeemed : 0);
        } else {
            $points = $isRedeemed ? $redeemed : $points;
        }

        // Do not redeem points if discount sum is less than minimal currency unit (e.g., 1 cent)
        // let the point be collected further instead
        $discountSum = LoyaltyProgram::getInstance()->calculateRedeemDiscount($points);
        if ($discountSum < $order->getCurrency()->getMinimumValue()) {
            return 0;
        }

        return $points;
    }

    /**
     * Whether the number of redeemed reward points can change for a placed order.
     *
     * @return boolean
     */
    protected function canRedeemedPointsChange()
    {
        return false;
    }

    /**
     * Check if it is the time to apply the modifier.
     *
     * @return boolean
     */
    protected function isTimeToApply()
    {
        // This modifier should be applied on the cart/order in two cases:
        // 1. Points were never used for the order and the modifier matches the selected "apply to" mode
        // 2. Points were used for the order (so we should execute the same modifier notwithstanding the configured "apply to" mode)

        $modifierMode = $this->getApplyMode();

        $result = LoyaltyProgram::getInstance()->getConfiguredApplyMode() == $modifierMode;

        if (!$result) {
            // If not the first case, we check the order's mode
            $order  = $this->getOrder();
            $result = $order->getPointsRedeemed()
                && ($this->getOrderModifierCode($order) == $this->getCode());
        }

        return $result;
    }

    /**
     * Scans order surcharges and returns the code of the reward modifier that was used for the order.
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return string
     */
    protected function getOrderModifierCode(\XLite\Model\Order $order)
    {
        $code = '';

        $loyaltyProgram = LoyaltyProgram::getInstance();
        foreach ($order->getSurcharges() as $surcharge) {
            if ($loyaltyProgram->isRewardPointsSurcharge($surcharge)) {
                $code = $surcharge->getCode();
            }
        }

        return $code;
    }

    /**
     * Returns the selected "Apply to" mode.
     *
     * @return integer
     */
    abstract protected function getApplyMode();
}
