<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

use XCart\Messenger\OAuthProviderFactory;

class AuthEmailFrom extends \XLite\View\FormField\Select\Regular
{
    public const AUTH_CUSTOM = 'custom';

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::AUTH_CUSTOM                         => 'Custom',
            OAuthProviderFactory::AUTH_OAUTH2_GOOGLE    => 'Google',
            OAuthProviderFactory::AUTH_OAUTH2_YAHOO     => 'Yahoo',
            OAuthProviderFactory::AUTH_OAUTH2_MICROSOFT => 'Microsoft',
        ];
    }
}
