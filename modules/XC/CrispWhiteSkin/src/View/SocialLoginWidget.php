<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Social sign-in widget
 * @Extender\Mixin
 */
abstract class SocialLoginWidget extends \CDev\SocialLogin\View\SocialLoginWidget
{
    use SocialLoginSeparatorTrait;
}
