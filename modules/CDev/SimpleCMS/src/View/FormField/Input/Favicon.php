<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\FormField\Input;

/**
 * Favicon
 */
class Favicon extends \CDev\SimpleCMS\View\FormField\Input\AImage
{
    /**
     * @return boolean
     */
    protected function isViaUrlAllowed()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function allowExtendedTypes()
    {
        return true;
    }
}
