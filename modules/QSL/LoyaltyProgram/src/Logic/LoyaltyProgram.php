<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic;

use XLite\Model\Profile;
use QSL\LoyaltyProgram\Model\RewardHistoryEvent as RewardEvent;
use XC\Reviews\Model\Review;

/**
 * Class handling common Loyalty Program function.
 */
class LoyaltyProgram extends \XLite\Base
{
    /**
     * Returns codes of modifiers related to Reward Points module.
     *
     * @return array
     */
    public static function getRewardPointsModifierCodes()
    {
        return [
            \QSL\LoyaltyProgram\Logic\Order\Modifier\RewardPointsSubtotal::MODIFIER_CODE,
            \QSL\LoyaltyProgram\Logic\Order\Modifier\RewardPointsTotal::MODIFIER_CODE,
        ];
    }

    /**
     * Check if the discout for used reward points should be applied on the subtotal.
     *
     * @return boolean
     */
    public function getConfiguredApplyMode()
    {
        return (int) \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_apply_to;
    }

    /**
     * Calculate the number of points the user will earn for paying the sum.
     *
     * @param float $sum The sum that we are calculating reward points for.
     *
     * @return integer
     */
    public function calculateEarnedRewardPoints($sum)
    {
        return floor($sum * $this->getEarnRate());
    }

    /**
     * Get the rate at which points are converted to discount.
     *
     * @return float
     */
    public function getEarnRate()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_earn_rate;
    }

    /**
     * Get the rate at which points are converted to discount.
     *
     * @return float
     */
    public function getRedeemRate()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_redeem_rate;
    }

    /**
     * Check if reward points should be calculated for orders with discount coupons.
     *
     * @return float
     */
    public function isRewardWithCouponEnabled()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_with_coupon;
    }

    /**
     * Calculate the number of points needed to cover the specified sum with a discount.
     *
     * @param float $sum The sum that we are calculating rewards points for.
     *
     * @return integer
     */
    public function calculatePointsToRedeem($sum)
    {
        $rate = $this->getRedeemRate();

        return ($rate > 0.000001) ? ceil($sum / $rate) : 0;
    }

    /**
     * Calcualte the discount that a customer will have after redeeming the specified number of reward points.
     *
     * @param integer $points Number of points to redeem.
     *
     * @return float
     */
    public function calculateRedeemDiscount($points)
    {
        return $points * $this->getRedeemRate();
    }

    /**
     * Calculate the maximum possible reward discount for an order.
     *
     * @param \XLite\Model\Order $order Order
     * @param mixed              $mode  Apply mode (uses the module settings if not specified) OPTIONAL
     *
     * @return float
     */
    public function calculateMaximumRewardDiscount(\XLite\Model\Order $order, $mode = null)
    {
        $total = $this->getRewardDiscountBase($order, $mode);
        $rate  = $this->getRewardPointsRedeemCap();

        if (preg_match('/^(\d+\.?\d*) *(%?)$/', trim($rate), $matches)) {
            $discount = ($matches[2] == '%')
                ? $total * $matches[1] / 100
                : $matches[1];
        } else {
            $discount = 0;
        }

        return min($discount, $total);
    }

    /**
     * Returns the base amount that reward discount should be calculated for.
     *
     * @param \XLite\Model\Order $order Order
     * @param integer            $mode  Apply mode (see ApplyDiscountTo class constants) OPTIONAL
     *
     * @return float
     */
    public function getRewardDiscountBase(\XLite\Model\Order $order, $mode = null)
    {
        $mode = is_null($mode) ? $this->getConfiguredApplyMode() : $mode;

        return ($mode === \QSL\LoyaltyProgram\View\FormField\Select\ApplyDiscountTo::MODE_APPLY_TO_SUBTOTAL)
            ? $this->getPreRewardOrderSubtotal($order)
            : $this->getPreRewardOrderTotal($order);
    }

    /**
     * The number of reward points a customer will earn for signing up in the store.
     *
     * @return integer
     */
    public function getSignupReward()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_signup_bonus;
    }

    /**
     * Maximum discount which a shopper can get for an order by redeeming his reward points.
     *
     * @return string
     */
    public function getRewardPointsRedeemCap()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_redeem_cap === ''
            ? '100%'
            : \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_redeem_cap;
    }

    /**
     * The number of reward points a customer will earn for reviewing a product.
     *
     * @return integer
     */
    public function getReviewReward()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_reviews_review;
    }

    /**
     * The number of reward points a customer will earn for rating a product.
     *
     * @return integer
     */
    public function getRatingReward()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_reviews_rate;
    }

    /**
     * Reward a customer for creating a store account.
     *
     * @param \XLite\Model\Profile $profile Profile
     */
    public function rewardForSignup(\XLite\Model\Profile $profile = null)
    {
        $points = $this->getSignupReward();

        if ($profile && $points) {
            $profile->addRewardPoints($points);

            \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                $profile,
                $points,
                RewardEvent::EVENT_REASON_SIGNUP_REWARD
            );
        }
    }

    /**
     * Updates user's reward points for a rating/review.
     *
     * @param integer                               $previousStatus Review status before the update.
     * @param integer                               $previousRating Review rating before the update.
     * @param string                                $previousText   Review text before the update.
     * @param \XLite\Model\Profile                  $profile        Reviewer's profile.
     * @param \XC\Reviews\Model\Review $review         Review after the update.
     *
     * @return boolean Whether the user's points have been updated, or not.
     */
    public function updateRewardForReview($previousStatus, $previousRating, $previousText, Profile $profile = null, Review $review = null)
    {
        $hadRatingOnly = !$previousText;

        $minRating = $this->getMinEligibleProductRating();

        $wasEligibleForReward = !is_null($previousStatus)           // The review had to have a status (i.e. had to exist)
            && ($previousStatus === Review::STATUS_APPROVED)        // ... and it had to be approved
            && ($previousRating >= $minRating);                     // ... and the rating had to be good

        $isEligibleForReward = ($review && $review->getId())        // The review should not be deleted
            && ($review->getStatus() === Review::STATUS_APPROVED)   // ... and it should be approved
            && ($review->getRating() >= $minRating);                // ... and the rating should be good

        // We should update rewards for the review in one the following cases:
        // 1. The review is eligible for the reward, and no reward has been given yet
        // 2. The review is no longer eligible for the reward that was given in past
        // 3. The reward type should be changed from "rating" to "review", or vice versa
        $updateNeeded = ($isEligibleForReward !== $wasEligibleForReward)              // either the status has changed
            || ($isEligibleForReward && ($hadRatingOnly !== !$review->getReview()));  // ... or the type (rating/review)

        if ($updateNeeded) {
            if ($wasEligibleForReward) {
                // Cancel reward for the review if it was given in past
                $this->cancelRewardForReview($profile, $hadRatingOnly);
            }
            if ($isEligibleForReward) {
                // Reward the customer for the review
                $this->rewardForReview($review->getProfile(), !$review->getReview());
            }
        }

        return $updateNeeded;
    }

    /**
     * Reward a customer for reviewing/rating a product.
     *
     * @param \XLite\Model\Profile $profile    Profile
     * @param boolean              $ratingOnly Whether we should reward for rating a product, or reviewing it
     */
    public function rewardForReview(\XLite\Model\Profile $profile = null, $ratingOnly = false)
    {
        $points = $ratingOnly ? $this->getRatingReward() : $this->getReviewReward();

        if ($profile && $points) {
            $profile->addRewardPoints($points);

            \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                $profile,
                $points,
                $ratingOnly ? RewardEvent::EVENT_REASON_RATING_REWARD : RewardEvent::EVENT_REASON_REVIEW_REWARD
            );
        }
    }

    /**
     * Cancel reward for reviewing/rating a product.
     *
     * @param \XLite\Model\Profile $profile    Profile
     * @param boolean              $ratingOnly Whether we should reward for rating a product, or reviewing it
     */
    public function cancelRewardForReview(\XLite\Model\Profile $profile = null, $ratingOnly = false)
    {
        $points = $ratingOnly ? $this->getRatingReward() : $this->getReviewReward();

        if ($profile && $points) {
            $profile->redeemRewardPoints($points);

            \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                $profile,
                -$points,
                $ratingOnly ? RewardEvent::EVENT_REASON_CANCELED_RATING : RewardEvent::EVENT_REASON_CANCELED_REVIEW
            );
        }
    }

    /**
     * Check if it is a reward points modifier.
     *
     * @param \XLite\Model\Order\Surcharge $surcharge
     *
     * @return boolean
     */
    public function isRewardPointsSurcharge(\XLite\Model\Order\Surcharge $surcharge)
    {
        return in_array(
            $surcharge->getCode(),
            self::getRewardPointsModifierCodes()
        );
    }

    /**
     * The customer will be eligible for the review/rate reward only if he rates a product not less than this setting.
     *
     * @return integer
     */
    public function getMinEligibleProductRating()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_reviews_min_rate;
    }

    /**
     * Get the order total as if no reward points were redeemed yet.
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    protected function getPreRewardOrderTotal(\XLite\Model\Order $order)
    {
        $total = $order->getSurchargesTotal();

        $discount = 0;
        foreach ($order->getSurcharges() as $surcharge) {
            if ($this->isRewardPointsSurcharge($surcharge)) {
                $discount += $surcharge->getValue();
            }
        }

        return $total - $discount;
    }

    /**
     * Get the order subtotal as if no reward points were redeemed yet.
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    protected function getPreRewardOrderSubtotal(\XLite\Model\Order $order)
    {
        return $order->getSubtotal();
    }
}
