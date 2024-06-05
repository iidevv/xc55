<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Order history main point of execution
 * @Extender\Mixin
 */
class OrderHistory extends \XLite\Core\OrderHistory
{
    /**
     * Codes for registered events of order history
     */
    public const CODE_SETTLE_REWARD_POINTS = 'SETTLE REWARD POINTS';
    public const CODE_REVERT_REWARD_POINTS = 'REVERT REWARD POINTS';

    /**
     * Texts for the order history event descriptions
     */
    public const TXT_SETTLE_REWARD_POINTS = '[Loyalty program] {{points}} reward points have been added to the user account';
    public const TXT_REVERT_REWARD_POINTS = '[Loyalty program] {{points}} reward points have been deducted from the user account';

    /**
     * Register "Settle reward points" event to the order history.
     *
     * @param integer $orderId Order identifier.
     * @param integer $points  Number of settler points.
     */
    public function registerSettleRewardPoints($orderId, $points)
    {
        $this->registerEvent(
            $orderId,
            static::CODE_SETTLE_REWARD_POINTS,
            $this->getSettleRewardPointsDescription($orderId),
            $this->getSettleRewardPointsData($orderId, $points)
        );
    }

    /**
     * Register "Revert reward points" event to the order history.
     *
     * @param integer $orderId Order identifier.
     * @param integer $points  Number of settler points.
     */
    public function registerRevertRewardPoints($orderId, $points)
    {
        $this->registerEvent(
            $orderId,
            static::CODE_REVERT_REWARD_POINTS,
            $this->getRevertRewardPointsDescription($orderId),
            $this->getRevertRewardPointsData($orderId, $points)
        );
    }

    /**
     * Text for Settle Reward Points description.
     *
     * @param integer $orderId Order identifier.
     *
     * @return string
     */
    protected function getSettleRewardPointsDescription($orderId)
    {
        return static::TXT_SETTLE_REWARD_POINTS;
    }

    /**
     * Data for Settle Reward Points description.
     *
     * @param integer $orderId Order identifier.
     * @param integer $points  Number of settler points.
     *
     * @return array
     */
    protected function getSettleRewardPointsData($orderId, $points)
    {
        return [
            'orderId' => $orderId,
            'points'  => $points,
        ];
    }

    /**
     * Text for Revert Reward Points description.
     *
     * @param integer $orderId Order identifier.
     *
     * @return string
     */
    protected function getRevertRewardPointsDescription($orderId)
    {
        return static::TXT_REVERT_REWARD_POINTS;
    }

    /**
     * Data for Revert Reward Points description.
     *
     * @param integer $orderId Order identifier.
     * @param integer $points  Number of settler points.
     *
     * @return array
     */
    protected function getRevertRewardPointsData($orderId, $points)
    {
        return [
            'orderId' => $orderId,
            'points'  => $points,
        ];
    }
}
