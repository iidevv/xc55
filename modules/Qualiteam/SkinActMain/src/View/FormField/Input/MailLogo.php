<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View\FormField\Input;


class MailLogo extends \CDev\SimpleCMS\View\FormField\Input\AImage
{
    /**
     * @return boolean
     */
    protected function isViaUrlAllowed()
    {
        return false;
    }
}
