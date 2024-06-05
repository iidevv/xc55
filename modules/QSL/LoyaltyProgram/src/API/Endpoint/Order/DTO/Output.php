<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\API\Endpoint\Order\DTO;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput as ExtendedOutput;
use QSL\LoyaltyProgram\API\Endpoint\Order\DTO\RewardHistoryEvent\OrderRewardHistoryEventOutput as RewardHistoryEventOutput;

/**
 * @Extender\Mixin
 */
class Output extends ExtendedOutput
{
    /**
     * @var int
     */
    public int $reward_points;

    /**
     * @var int
     */
    public int $settled_points;

    /**
     * @var int
     */
    public int $redeemed_points;

    /**
     * @var int
     */
    public int $max_redeemed_points;

    /**
     * @var RewardHistoryEventOutput[]
     */
    public array $reward_events;

    /**
     * @var bool
     */
    public bool $points_rewarded;

    /**
     * @var bool
     */
    public bool $points_redeemed;
}
