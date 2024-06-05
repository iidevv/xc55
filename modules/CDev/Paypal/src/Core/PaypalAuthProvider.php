<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend("CDev\SocialLogin")
 */
class PaypalAuthProvider extends \CDev\SocialLogin\Core\AAuthProvider
{
    /**
     * Unique auth provider name
     */
    public const PROVIDER_NAME = 'paypal';

    /**
     * Url to which user will be redirected
     */
    public const AUTH_REQUEST_URL = '';

    /**
     * Data to gain access to
     */
    public const AUTH_REQUEST_SCOPE = '';

    /**
     * Url to get access token
     */
    public const TOKEN_REQUEST_URL = '';

    /**
     * Url to access user profile information
     */
    public const PROFILE_REQUEST_URL = '';

    /**
     * Get OAuth 2.0 client ID
     *
     * @return string
     */
    protected function getClientId()
    {
        return \XLite\Core\Config::getInstance()->CDev->Paypal->loginClientId;
    }

    /**
     * Get OAuth 2.0 client secret
     *
     * @return string
     */
    protected function getClientSecret()
    {
        return \XLite\Core\Config::getInstance()->CDev->Paypal->loginClientSecret;
    }
}
