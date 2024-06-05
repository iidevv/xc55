<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\API\Endpoint\Profile\DTO;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Profile\DTO\ProfileInput as ExtendedInput;

/**
 * @Extender\Mixin
 */
class ProfileInput extends ExtendedInput
{
    /**
     * @var int|null
     */
    public ?int $reward_points = 0;
}
