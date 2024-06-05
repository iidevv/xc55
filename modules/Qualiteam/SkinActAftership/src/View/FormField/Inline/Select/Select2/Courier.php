<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\FormField\Inline\Select\Select2;

use Qualiteam\SkinActAftership\View\FormField\Select\Select2\Courier as CourierInline;

use XLite\View\FormField\Inline\Base\Single;

class Courier extends Single
{
    /**
     * @inheritDoc
     */
    protected function defineFieldClass()
    {
        return CourierInline::class;
    }
}