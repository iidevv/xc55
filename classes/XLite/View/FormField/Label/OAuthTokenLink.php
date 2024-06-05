<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Label;

use XLite\Core\Config;

/**
 * ReplyToCustomer
 */
class OAuthTokenLink extends \XLite\View\FormField\Label
{
    protected function getLabelValue()
    {
        if ($this->getToken()) {
            return static::t('The token has already been generated', [
                'url'  => $this->getURL()
            ]);
        }

        if (Config::getInstance()->Email->smtp_client_id && Config::getInstance()->Email->smtp_secret_key) {
            return static::t('Generate auth token', ['url' => $this->getURL()]);
        }

        return static::t('To generate an authentication token, first enter the Client ID and Secret key, then save the changes');
    }

    /**
     * @return string
     */
    protected function getURL()
    {
        return $this->buildURL('email_settings', 'oauth');
    }

    protected function getToken(): ?\stdClass
    {
        return \json_decode(Config::getInstance()->Email->smtp_auth_token, false);
    }
}
