<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Popup;

/**
 * Impose own inner widget class
 */
class EnterNewPassword extends \XLite\View\Popup\ForceChangePassword
{
    protected function getInnerWidgetClass()
    {
        return '\XLite\View\Model\Profile\EnterNewPassword';
    }
}
