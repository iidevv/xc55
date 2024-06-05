<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

class EmailOAuthReturnUrl extends \XLite\View\FormField\Input\Text
{
    public function getValue()
    {
        return $this->buildFullURL('email_settings', 'oauth_return');
    }
}
