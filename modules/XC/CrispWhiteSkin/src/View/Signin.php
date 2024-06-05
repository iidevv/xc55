<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Signin
 * @Extender\Mixin
 */
abstract class Signin extends \XLite\View\Signin
{
    protected function getWrapperStyleClass()
    {
        return 'signin-wrapper';
    }
}
