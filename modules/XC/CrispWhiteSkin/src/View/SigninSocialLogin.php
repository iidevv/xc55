<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Signin
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SocialLogin")
 * @Extender\After("XC\CrispWhiteSkin")
 */
abstract class SigninSocialLogin extends \XLite\View\Signin
{
    protected function getWrapperStyleClass()
    {
        $result = parent::getWrapperStyleClass();

        if (\CDev\SocialLogin\Core\AuthManager::getAuthProviders()) {
            $result .= ' social-login';
        }

        return $result;
    }
}
