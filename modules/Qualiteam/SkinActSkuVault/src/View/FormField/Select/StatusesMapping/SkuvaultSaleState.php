<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping;

use XLite\View\FormField\Inline\Base\Single;

class SkuvaultSaleState extends Single
{
    protected function defineFieldClass()
    {
        return Inline\SkuvaultSaleState::class;
    }
}
