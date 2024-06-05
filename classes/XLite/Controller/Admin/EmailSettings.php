<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XCart\Messenger\OAuthProviderFactory;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\Request;

class EmailSettings extends \XLite\Controller\Admin\Settings
{
    public $page = self::EMAIL_PAGE;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Email transfer');
    }

    public static function needFormId()
    {
        return !in_array(Request::getInstance()->action, ['oauth', 'oauth_return'], true)
            && parent::needFormId();
    }

    /**
     * @return bool
     */
    public function isQueuesNoteVisible(): bool
    {
        return \Includes\Utils\ConfigParser::getOptions(['performance', 'background_jobs']);
    }

    public function doActionUpdate()
    {
        if (Request::resetInstance()->Email->smtp_auth_mode !== Config::getInstance()->Email->smtp_auth_mode) {
            $this->updateAccessToken();
        }

        parent::doActionUpdate();
    }

    protected function doActionOauth()
    {
        $clientId = Config::getInstance()->Email->smtp_client_id;
        $secretKey = Config::getInstance()->Email->smtp_secret_key;
        $providerName = Config::getInstance()->Email->smtp_auth_mode;

        if (empty($clientId) || empty($secretKey) || !static::isOAuth()) {
            \XLite\Core\TopMessage::addWarning('Authentication mode not configured');
            $redirectUrl = $this->buildURL('email_settings');
        } else {
            $oAuthConfig = OAuthProviderFactory::create(
                $providerName,
                $clientId,
                $secretKey,
                $this->buildURL('email_settings', 'oauth_return')
            );

            $redirectUrl = $oAuthConfig
                ->getProvider()
                ->getAuthorizationUrl(
                    $oAuthConfig->getOptions()
                );
        }

        $this->redirect($redirectUrl);
    }

    protected function doActionOauthReturn()
    {
        if (($code = Request::getInstance()->code) && static::isOAuth()) {
            $oAuthConfig = OAuthProviderFactory::create(
                Config::getInstance()->Email->smtp_auth_mode,
                Config::getInstance()->Email->smtp_client_id,
                Config::getInstance()->Email->smtp_secret_key,
                $this->buildURL('email_settings', 'oauth_return')
            );

            $token = $oAuthConfig->getProvider()->getAccessToken(
                'authorization_code',
                [
                    'code' => $code
                ]
            );

            if ($token) {
                $this->updateAccessToken(\json_encode($token));
                \XLite\Core\TopMessage::addInfo('OAuth Refresh token save');
            }
        }

        $this->redirect($this->buildURL('email_settings'));
    }

    protected function updateAccessToken(string $value = '')
    {
        /** @var \XLite\Model\Repo\Config $configRepo */
        $configRepo = Database::getRepo('\XLite\Model\Config');

        /** @var \XLite\Model\Config|null $smtpRefreshToken */
        $smtpRefreshToken = $configRepo->findOneBy([
            'category' => 'Email',
            'name' => 'smtp_auth_token'
        ]);

        if ($smtpRefreshToken) {
            $smtpRefreshToken->setValue($value);
            $configRepo->update($smtpRefreshToken);
            \XLite\Core\Config::updateInstance();
        }
    }

    protected static function isOAuth(): bool
    {
        $authMode = Config::getInstance()->Email->smtp_auth_mode;
        $OAuthProviderNames = [
            OAuthProviderFactory::AUTH_OAUTH2_GOOGLE,
            OAuthProviderFactory::AUTH_OAUTH2_YAHOO,
            OAuthProviderFactory::AUTH_OAUTH2_MICROSOFT
        ];

        return in_array($authMode, $OAuthProviderNames, true);
    }
}
