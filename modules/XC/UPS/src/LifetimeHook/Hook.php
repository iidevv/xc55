<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\LifetimeHook;

class Hook
{
    public function onInit(): void
    {
        // Register UPS shipping processor
        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'XC\UPS\Model\Shipping\Processor\UPS'
        );
    }
}
