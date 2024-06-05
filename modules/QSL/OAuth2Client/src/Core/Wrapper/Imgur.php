<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Imgur wrapper
 */
class Imgur extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId'        => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Client ID',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Client secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Authorization callback URL',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \AdamPaterson\OAuth2\Client\Provider\Imgur(
            [
                'clientId'     => $this->provider->getSetting('clientId'),
                'clientSecret' => $this->provider->getSetting('clientSecret'),
                'redirectUri'  => $this->getRedirectURL(),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \AdamPaterson\OAuth2\Client\Provider\ImgurResourceOwner) {
            /** @var \AdamPaterson\OAuth2\Client\Provider\ImgurResourceOwner $resource */
            $user->accountURL = 'https://imgur.com/user/' . $resource->getUrl();

            $response = $this->requestAuthenticated(
                'https://api.imgur.com/3/account/' . $resource->getUrl() . '/settings',
                $token->getToken()
            );
            if ($response && !empty($response['data']) && !empty($response['data']['email'])) {
                $user->email = $response['data']['email'];
            }
        }

        return $user;
    }
}
