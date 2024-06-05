<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action;

use CDev\GoogleAnalytics\Core\GA;

trait DataDrivenAction
{
    protected static function getActionType(): string
    {
        return 'data-driven';
    }

    public function isApplicable(): bool
    {
        /** @noinspection PhpMultipleClassDeclarationsInspection */
        return parent::isApplicable()
            && GA::getResource()->isECommerceEnabled();
    }
}
