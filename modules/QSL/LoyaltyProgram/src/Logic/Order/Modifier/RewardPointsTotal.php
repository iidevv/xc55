<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic\Order\Modifier;

/**
 * Discount for reward points the shopper spend on the order.
 */
class RewardPointsTotal extends \QSL\LoyaltyProgram\Logic\Order\Modifier\ARewardPoints
{
    /**
     * Modifier code is the same as a base Discount - this will be aggregated to the single 'Discount' line in cart totals.
     */
    public const MODIFIER_CODE = 'REWARDPOINTS';

    /**
     * Modifier unique code.
     *
     * @var string
     */
    protected $code = self::MODIFIER_CODE;

    /**
     * Returns the selected "Apply to" mode.
     *
     * @return integer
     */
    protected function getApplyMode()
    {
        return \QSL\LoyaltyProgram\View\FormField\Select\ApplyDiscountTo::MODE_APPLY_TO_TOTAL;
    }
}
