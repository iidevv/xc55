<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\API\Endpoint\Profile\DTO;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileOutput as ExtendedOutput;

/**
 * @Extender\Mixin
 */
class ProfileOutput extends ExtendedOutput
{
    /**
     * @var int|null
     */
    public ?int $reward_points = 0;
}
