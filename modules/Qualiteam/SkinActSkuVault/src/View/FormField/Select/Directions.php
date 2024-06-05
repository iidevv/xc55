<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select;

use XLite\View\FormField\Select\ASelect;

class Directions extends ASelect
{
    const DIR_ANY            = '';
    const DIR_SKUVAULT_TO_XC = 'SX';
    const DIR_XC_TO_SKUVAULT = 'XS';

    const DIRECTIONS = [
        self::DIR_ANY            => 'Any (SkuVault to X-Cart and/or X-Cart to SkuVault)',
        self::DIR_SKUVAULT_TO_XC => 'SkuVault to X-Cart',
        self::DIR_XC_TO_SKUVAULT => 'X-Cart to SkuVault',
    ];

    /**
     * @inheritDoc
     */
    protected function getDefaultOptions()
    {
        return self::DIRECTIONS;
    }
}
