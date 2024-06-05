<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace CDev\FedEx\LifetimeHook;

class Hook
{
    public function onInit(): void
    {
        // Register FedEx shipping processor
        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'CDev\FedEx\Model\Shipping\Processor\FEDEX'
        );
    }
}
