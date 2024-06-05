<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\FormField\Input\Text;


class Email extends \XLite\View\FormField\Input\Text\Email
{
    protected function isRequired()
    {
        return true;
    }
}
