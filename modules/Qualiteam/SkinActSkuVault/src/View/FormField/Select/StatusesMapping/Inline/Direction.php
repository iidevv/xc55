<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping\Inline;

use Qualiteam\SkinActSkuVault\Model\StatusesMap;
use XLite\View\FormField\Select\ASelect;

class Direction extends ASelect
{
    const OPTIONS = [
        StatusesMap::DIRECTION_XC_TO_SKUVAULT => 'X-Cart to SkuVault',
        StatusesMap::DIRECTION_SKUVAULT_TO_XC => 'SkuVault to X-Cart',
    ];

    protected function getDefaultOptions()
    {
        return static::OPTIONS;
    }
}
