<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck;

interface RuleInterface
{
    /**
     * @return Inconsistency|bool
     */
    public function execute();
}
