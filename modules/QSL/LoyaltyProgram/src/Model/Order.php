<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Order\Status\Payment;
use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Number of reward points the user will earn for the order.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $rewardPoints = 0;

    /**
     * Number of reward points given to the user for the order.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $settledPoints = 0;

    /**
     * Number of reward points the shopper redeems with this order.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $redeemedPoints = 0;

    /**
     * The maximum number of reward points the shopper wants to redeem with this order.
     * A negative value means "no limit".
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $maxRedeemedPoints = -1;

    /**
     * Reward events associated with the order.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\LoyaltyProgram\Model\RewardHistoryEvent", mappedBy="order", cascade={"all"})
     */
    protected $rewardEvents;

    /**
     * Whether points were rewarded for the order, or not.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $pointsRewarded = false;

    /**
     * Whether points were redeemed for the order, or not.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $pointsRedeemed = false;

    /**
     * Cached number of reward points used for the order already.
     *
     * @var int
     */
    protected $usedRewardPoints = 0;

    /**
     * Get the number of reward points the user will earn for the order.
     *
     * @return integer
     */
    public function getCalculatedRewardPoints()
    {
        return $this->rewardPoints;
    }

    /**
     * Set the number of reward points the user will earn for the order.
     *
     * @param integer $points Number of points.
     */
    public function setCalculatedRewardPoints($points)
    {
        $this->rewardPoints = $points;
    }

    /**
     * Check whether the loyalty program is enabled for the shopper.
     *
     * @return boolean
     */
    public function isUserInLoyaltyProgram()
    {
        $profile = $this->getOrigProfile() ?: $this->getProfile();

        return $profile && $profile->isLoyaltyProgramEnabled();
    }

    /**
     * Get the number of reward points given to the user for the order.
     *
     * @return integer
     */
    public function getSettledRewardPoints()
    {
        return $this->settledPoints;
    }

    /**
     * Set the number of reward points given to the user for the order.
     *
     * @param integer $points Number of points.
     */
    public function setSettledRewardPoints($points)
    {
        $this->settledPoints = $points;
    }

    /**
     * Get the number of reward points the shopper redeems with the order.
     *
     * @return integer
     */
    public function getRedeemedRewardPoints()
    {
        return $this->redeemedPoints;
    }

    /**
     * Set the number of reward points the shopper redeems with the order.
     *
     * @param integer $points Number of points.
     */
    public function setRedeemedRewardPoints($points)
    {
        $this->redeemedPoints = $points;
    }

    /**
     * Get the maximum number of reward points the shopper wants to redeem with the order.
     * A negative value means "no limit".
     *
     * @return integer
     */
    public function getMaxRedeemedRewardPoints()
    {
        return $this->maxRedeemedPoints;
    }

    /**
     * Set the maximum number of reward points the shopper wants to redeem with the order.
     *
     * @param integer $points Number of points. A negative value means "no limit".
     */
    public function setMaxRedeemedRewardPoints($points)
    {
        $this->maxRedeemedPoints = $points;
    }

    /**
     * Calculate order.
     */
    public function calculate()
    {
        $this->resetNumberOfUsedRewardPoints();

        parent::calculate();

        $this->updateCalculatedRewardPoints();
        // $this->updateMaxRedeemedRewardPoints();
    }

    /**
     * Calculates the number of reward points that are required give the
     * maximum discount for the order.
     *
     * @param mixed $mode Apply mode (uses the module settings if not specified) OPTIONAL
     *
     * @return int
     */
    public function calculateRequiredRewardPoints($mode = null)
    {
        $loyaltyProgram = LoyaltyProgram::getInstance();

        // The maximum discount the shopper may ever get for the order
        $maxDiscount = $loyaltyProgram->calculateMaximumRewardDiscount($this, $mode);

        // The number of points required for the maximum discount
        return $loyaltyProgram->calculatePointsToRedeem($maxDiscount);
    }

    /**
     * Status change handler for the "reward" event.
     */
    public function processReward()
    {
        if (!$this->getPointsRewarded()) {
            $this->settleRewardPoints();
        }
    }

    /**
     * Status change handler for the "redeemPoints" event.
     */
    public function processRedeemPoints()
    {
        if (!$this->getPointsRedeemed()) {
            $this->redeemRewardPoints();
        }
    }

    /**
     * Status change handler for the "unredeemPoints" event.
     */
    public function processUnredeemPoints()
    {
        if ($this->getPointsRedeemed()) {
            $this->unredeemRewardPoints();
        }
    }

    /**
     * Number of rewards points used by modifiers for the order already.
     * Used in modifier calculations only.
     *
     * @return int
     */
    public function resetNumberOfUsedRewardPoints()
    {
        $this->usedRewardPoints = 0;
    }

    /**
     * Number of rewards points used by modifiers for the order already.
     * Used in modifier calculations only.
     *
     * @return int
     */
    public function getNumberOfUsedRewardPoints()
    {
        return $this->usedRewardPoints;
    }

    /**
     * Increases the number of points used for the order by modifiers.
     * Used in modifier calculations only.
     *
     * @param int $points Number of used points
     */
    public function addUsedRewardPoints($points)
    {
        $this->usedRewardPoints += $points;
    }

    /**
     * Called when an order successfully placed by a client
     */
    public function processSucceed()
    {
        parent::processSucceed();

        $this->processSucceedLoyaltyProgram();
    }

    /**
     * Retu4rns the number of reward points the user will earn for the order.
     *
     * @return integer
     */
    public function getRewardPoints()
    {
        return $this->rewardPoints;
    }

    /**
     * Updates the number of reward points the user will earn for the order.
     *
     * @param integer $rewardPoints Number of points
     *
     * @return Order
     */
    public function setRewardPoints($rewardPoints)
    {
        $this->rewardPoints = $rewardPoints;

        return $this;
    }

    /**
     * Returns the number of reward points given to the user for the order.
     *
     * @return integer
     */
    public function getSettledPoints()
    {
        return $this->settledPoints;
    }

    /**
     * Updates the number of reward points given to the user for the order.
     *
     * @param integer $settledPoints Number of points
     *
     * @return Order
     */
    public function setSettledPoints($settledPoints)
    {
        $this->settledPoints = $settledPoints;

        return $this;
    }

    /**
     * Returns the number of reward points the user applied on the order.
     *
     * @return integer
     */
    public function getRedeemedPoints()
    {
        return $this->redeemedPoints;
    }

    /**
     * Updates the number of reward points the user applied on the order.
     *
     * @param integer $redeemedPoints Number of points
     *
     * @return Order
     */
    public function setRedeemedPoints($redeemedPoints)
    {
        $this->redeemedPoints = $redeemedPoints;

        return $this;
    }

    /**
     * Returns the maximum number of reward points the user wants to apply on the order.
     *
     * @return integer
     */
    public function getMaxRedeemedPoints()
    {
        return $this->maxRedeemedPoints;
    }

    /**
     * Updates the maximum number of reward points the user wants to apply on the order.
     *
     * @param integer $maxRedeemedPoints Number of points
     *
     * @return Order
     */
    public function setMaxRedeemedPoints($maxRedeemedPoints)
    {
        $this->maxRedeemedPoints = $maxRedeemedPoints;

        return $this;
    }

    /**
     * Checks if reward points are rewarded for the order.
     *
     * @return boolean
     */
    public function getPointsRewarded()
    {
        return $this->pointsRewarded;
    }

    /**
     * Configures whether reward points are rewarded for the order, or not.
     *
     * @param boolean $pointsRewarded Status
     *
     * @return Order
     */
    public function setPointsRewarded($pointsRewarded)
    {
        $this->pointsRewarded = $pointsRewarded;

        return $this;
    }

    /**
     * Checks if reward points are redeemed for the order.
     *
     * @return boolean
     */
    public function getPointsRedeemed()
    {
        return $this->pointsRedeemed;
    }

    /**
     * Configures whether reward points are redeemed for the order, or not.
     *
     * @param boolean $pointsRedeemed Status
     *
     * @return Order
     */
    public function setPointsRedeemed($pointsRedeemed)
    {
        $this->pointsRedeemed = $pointsRedeemed;

        return $this;
    }

    /**
     * Associates a reward event with the order.
     *
     * @param \QSL\LoyaltyProgram\Model\RewardHistoryEvent $rewardEvents Reward event model
     *
     * @return Order
     */
    public function addRewardEvents(\QSL\LoyaltyProgram\Model\RewardHistoryEvent $rewardEvents)
    {
        $this->rewardEvents[] = $rewardEvents;

        return $this;
    }

    /**
     * Returns reward events associated with the order.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRewardEvents()
    {
        return $this->rewardEvents;
    }

    /**
     * Updates the number of reward points the user will earn for the order.
     */
    protected function updateCalculatedRewardPoints()
    {
        $this->setCalculatedRewardPoints($this->calculateRewardPoints());
    }

    /**
     * Adjust the user limit set on the redeemed reward points to make it not
     * exceed the maximum number of available points.
     */
    protected function updateMaxRedeemedRewardPoints()
    {
        if ($this->isParent()) {
            $points = $this->calculateRequiredRewardPoints();
            // Update the user max discount if it is above the max number of points that can be redeemed
            if ($this->getMaxRedeemedRewardPoints() > $points) {
                $this->setMaxRedeemedRewardPoints($points);
            }
        }
    }

    /**
     * Calculate reward points the user will earn for the order.
     *
     * @return integer
     */
    protected function calculateRewardPoints()
    {
        $points = 0;

        if ($this->canCalculateRewardPoints()) {
            $convertedSum = 0;

            // Calculate points for products in the cart
            foreach ($this->getItems() as $item) {
                $product = $item->getProduct();
                if ($product && $product->hasDefinedRewardPoints()) {
                    $points       += $product->getRewardPoints() * $item->getAmount();
                    $convertedSum += $item->calculateTotal();
                }
            }

            // Calculate points for the order sum
            $points += $this->calculateRewardPointsByRate(
                [
                    $this->getTotal(),
                    -$this->getRefundedAmount(),
                    -$this->getShippingCostExcludedFromRewards(),
                    -$this->getTaxesExcludedFromRewards(),
                    -$convertedSum,
                ]
            );
        }

        return $points;
    }

    /**
     * Calculates reward points for a sum of subtotals.
     *
     * @param array $subtotals Reward points will be calculated for the sum of these subtotals.
     *
     * @return float
     */
    protected function calculateRewardPointsByRate(array $subtotals)
    {
        $sum = 0;

        $currency = $this->getCurrency();
        foreach ($subtotals as $subtotal) {
            $sum += $currency->roundValue($subtotal);
        }

        return (0 < $sum)
            ? LoyaltyProgram::getInstance()->calculateEarnedRewardPoints($sum)
            : 0;
    }

    /**
     * Returns the shipping cost sum that points should not be awarded for.
     *
     * @return float
     */
    protected function getShippingCostExcludedFromRewards()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_for_shipping
            ? 0
            : $this->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);
    }

    /**
     * Returns the sum of taxes that points should not be awarded for.
     *
     * @return float
     */
    protected function getTaxesExcludedFromRewards()
    {
        return \XLite\Core\Config::getInstance()->QSL->LoyaltyProgram->reward_points_for_taxes
            ? 0
            : $this->getRewardTaxesSum();
    }

    /**
     * Returns the total sum of applied taxes.
     *
     * @return float
     */
    protected function getRewardTaxesSum()
    {
        $total = 0;

        foreach ($this->getSurcharges() as $surcharge) {
            if ($surcharge->getType() === \XLite\Model\Base\Surcharge::TYPE_TAX) {
                $total += $surcharge->getValue();
            }
        }

        return $total;
    }

    /**
     * Check if the user can earn reward points for the order.
     *
     * @return boolean
     */
    protected function canCalculateRewardPoints()
    {
        // We have to check if Coupons module is enabled via method_exists()
        // function instead of @Decorator\Depend because Loyalty Program module
        // has other classes that decorate Order model, and having one more
        // class doing the same results into "Circular dependency found" error.
        // So, we may calculate reward points if any of the following is true:
        // 1. Coupons module is disabled (there is no getUsedCoupons() method)
        // 2. LoyaltyProgram is configured to reward for orders with coupons
        // 3. There are no coupons used for the order
        return !method_exists($this, 'getUsedCoupons')
            || LoyaltyProgram::getInstance()->isRewardWithCouponEnabled()
            || !count($this->getUsedCoupons());
    }

    /**
     * Get the refunded amount for the order.
     *
     * @return float
     */
    protected function getRefundedAmount()
    {
        $amount = 0.0;

        if (!($this instanceof \XLite\Model\Cart)) {
            // This method triggers a warning when used for the cart model
            $transactions = $this->getPaymentTransactionSums();

            $amount = $transactions[(string)static::t('Refunded amount')] ?? 0.0;
        }

        return $amount;
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @param mixed  $oldStatus Old order status
     * @param mixed  $newStatus New order status
     * @param string $type      Type
     *
     * @return string|array
     */
    protected function getStatusHandlers($oldStatus, $newStatus, $type)
    {
        $handlers = parent::getStatusHandlers($oldStatus, $newStatus, $type);

        if ($this->isEligibleForRewardHandlers($type)) {
            $oldCode = $oldStatus->getCode();
            $newCode = $newStatus->getCode();

            $rewardHandlers = $this->getRewardStatusHandlers();

            if ($oldCode && $newCode && isset($rewardHandlers[$oldCode][$newCode])) {
                $handlers = array_merge($handlers, $rewardHandlers[$oldCode][$newCode]);
            }
        }

        return $handlers;
    }

    /**
     * Check if the entity is eligble for executing Loaylty Program status handlers.
     *
     * @param string $type      Type
     *
     * @return boolean
     */
    protected function isEligibleForRewardHandlers($type)
    {
        return ($type === 'payment');
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @return string|array
     */
    protected function getRewardStatusHandlers()
    {
        return [
            Payment::STATUS_QUEUED     => [
                Payment::STATUS_PAID     => ['reward'],
                Payment::STATUS_DECLINED => ['unredeemPoints'],
                Payment::STATUS_CANCELED => ['unredeemPoints'],
            ],
            Payment::STATUS_DECLINED   => [
                Payment::STATUS_AUTHORIZED => ['redeemPoints'],
                Payment::STATUS_PART_PAID  => ['redeemPoints'],
                Payment::STATUS_PAID       => ['reward', 'redeemPoints'],
                Payment::STATUS_QUEUED     => ['redeemPoints'],
            ],
            Payment::STATUS_CANCELED   => [
                Payment::STATUS_AUTHORIZED => ['redeemPoints'],
                Payment::STATUS_PART_PAID  => ['redeemPoints'],
                Payment::STATUS_PAID       => ['reward', 'redeemPoints'],
                Payment::STATUS_QUEUED     => ['redeemPoints'],
            ],
            Payment::STATUS_REFUNDED   => [
                Payment::STATUS_DECLINED => ['unredeemPoints'],
                Payment::STATUS_CANCELED => ['unredeemPoints'],
                Payment::STATUS_PAID     => ['partialReward'],
            ],
            Payment::STATUS_AUTHORIZED => [
                Payment::STATUS_DECLINED => ['unredeemPoints'],
                Payment::STATUS_CANCELED => ['unredeemPoints'],
                Payment::STATUS_PAID     => ['reward'],
            ],
            Payment::STATUS_PART_PAID  => [
                Payment::STATUS_DECLINED => ['unredeemPoints'],
                Payment::STATUS_CANCELED => ['unredeemPoints'],
                Payment::STATUS_PAID     => ['reward'],
            ],
            Payment::STATUS_PAID       => [
                Payment::STATUS_QUEUED     => ['cancelRewards'],
                Payment::STATUS_DECLINED   => ['cancelRewards', 'unredeemPoints'],
                Payment::STATUS_CANCELED   => ['cancelRewards', 'unredeemPoints'],
                Payment::STATUS_REFUNDED   => ['cancelRewards'],
                Payment::STATUS_AUTHORIZED => ['cancelRewards'],
                Payment::STATUS_PART_PAID  => ['cancelRewards'],
            ],
        ];
    }

    /**
     * @param string $type Type
     *
     * @return array
     */
    protected function getStatusHandlersForCast($type)
    {
        if ($this->isEligibleForRewardHandlers($type)) {
            return array_merge_recursive(parent::getStatusHandlersForCast($type), $this->getRewardStatusHandlers());
        } else {
            return parent::getStatusHandlersForCast($type);
        }
    }

    /**
     * Status change handler for the "cancelReward" event.
     */
    protected function processCancelRewards()
    {
        if ($this->getPointsRewarded()) {
            $this->cancelSettledRewardPoints();
        }
    }

    /**
     * Status change handler for the "partialReward" event.
     */
    protected function processPartialReward()
    {
        $this->adjustSettledRewardPoints();
    }

    /**
     * Cancel reward points awarded to the shopper for the order.
     */
    protected function cancelSettledRewardPoints()
    {
        $profile = $this->getOrigProfile();
        $points  = $this->getSettledRewardPoints();

        if ($profile && (0 < $points)) {
            $profile->redeemRewardPoints($points);
            $this->setSettledRewardPoints(0);
            $this->setPointsRewarded(false);
            \XLite\Core\OrderHistory::getInstance()->registerRevertRewardPoints($this->getOrderId(), $points);

            \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                $profile,
                -1 * $points,
                RewardHistoryEvent::EVENT_REASON_CANCELED_REWARD,
                '',
                $this
            );
        }
    }

    /**
     * Award points to the shopper.
     */
    protected function settleRewardPoints()
    {
        $profile = $this->getOrigProfile() ?: $this->getProfile();
        $points  = $this->getCalculatedRewardPoints();

        // Make sure we do not settle reward points twice for the same order
        if ($profile && (0 < $points) && !$this->getSettledRewardPoints()) {
            $profile->addRewardPoints($points);
            $this->setSettledRewardPoints($points);
            $this->setPointsRewarded(true);
            \XLite\Core\OrderHistory::getInstance()->registerSettleRewardPoints($this->getOrderId(), $points);

            \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                $profile,
                $points,
                RewardHistoryEvent::EVENT_REASON_ORDER_REWARD,
                '',
                $this
            );
        }
    }

    /**
     * Adjust reward points awarded to the shopper.
     */
    protected function adjustSettledRewardPoints()
    {
        $profile = $this->getOrigProfile();

        $adjusted   = $this->calculateRewardPoints();
        $settled    = $this->getSettledRewardPoints();
        $adjustment = intval($adjusted - $settled);

        $pointsRewarded = intval($adjusted) > 0;

        if ($profile && ($this->getPointsRewarded() !== $pointsRewarded)) {
            if ($adjustment) {
                $this->setPointsRewarded($pointsRewarded);
            }

            if ($adjustment > 0) {
                $profile->addRewardPoints($adjustment);
                $this->setSettledRewardPoints($adjusted);
                \XLite\Core\OrderHistory::getInstance()->registerSettleRewardPoints($this->getOrderId(), $adjustment);
                \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                    $profile,
                    $adjustment,
                    RewardHistoryEvent::EVENT_REASON_ORDER_REWARD,
                    '',
                    $this
                );
            } elseif ($adjustment < 0) {
                $profile->redeemRewardPoints(abs($adjustment));
                $this->setSettledRewardPoints($adjusted);
                \XLite\Core\OrderHistory::getInstance()->registerRevertRewardPoints($this->getOrderId(), abs($adjustment));
                \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                    $profile,
                    $adjustment,
                    RewardHistoryEvent::EVENT_REASON_CANCELED_REWARD,
                    '',
                    $this
                );
            }
        }
    }

    /**
     * Redeem shopper's reward points.
     */
    protected function redeemRewardPoints()
    {
        $profile = $this->getOrigProfile();
        $points  = $this->getRedeemedRewardPoints();

        if ($profile && (0 < $points)) {
            $profile->redeemRewardPoints($points);
            $this->setPointsRedeemed(true);
            \XLite\Core\OrderHistory::getInstance()->registerRevertRewardPoints($this->getOrderId(), $points);

            \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                $profile,
                -1 * $points,
                RewardHistoryEvent::EVENT_REASON_REDEEMED_POINTS,
                '',
                $this
            );
        }
    }

    /**
     * Return redeemed reward points back to the shopper.
     */
    protected function unredeemRewardPoints()
    {
        $profile = $this->getOrigProfile();
        $points  = $this->getRedeemedRewardPoints();

        if ($profile && (0 < $points) && !$this->getSettledRewardPoints()) {
            $profile->addRewardPoints($points);
            $this->setPointsRedeemed(false);
            \XLite\Core\OrderHistory::getInstance()->registerSettleRewardPoints($this->getOrderId(), $points);

            \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                $profile,
                $points,
                RewardHistoryEvent::EVENT_REASON_RETURNED_POINTS,
                '',
                $this
            );
        }
    }

    /**
     * processSucceed handler logic added by Loyalty Program module.
     */
    protected function processSucceedLoyaltyProgram()
    {
        // Remove applied reward points from the user's account when the cart becomes order
        $this->processRedeemPoints();
    }
}
