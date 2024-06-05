<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\SocialLogin")
 */
class AuthManager extends \CDev\SocialLogin\Core\AuthManager
{
    /**
     * Get all available authentication providers class names
     *
     * @return array List of auth provider class names (\CDev\SocialLogin\Core\AAuthProvider descendants)
     */
    protected static function getAuthProvidersClassNames()
    {
        $list = parent::getAuthProvidersClassNames();
        $list[] = 'CDev\Paypal\Core\PaypalAuthProvider';

        return $list;
    }
}
