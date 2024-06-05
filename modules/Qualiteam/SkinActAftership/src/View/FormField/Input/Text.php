<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\FormField\Input;

/**
 * Class text
 */
class Text extends \XLite\View\FormField\Input\Text
{
    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize(): int
    {
        return 500;
    }
}