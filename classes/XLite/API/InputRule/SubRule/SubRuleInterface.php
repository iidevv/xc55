<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\InputRule\SubRule;

use ApiPlatform\Core\Exception\InvalidArgumentException;

interface SubRuleInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function check(object $inputDTO, array $context): void;
}
