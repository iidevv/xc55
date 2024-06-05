<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Google wrapper
 */
class Google extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
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
        $url = $this->getRedirectURL();
        $parts = parse_url($url);

        return new \League\OAuth2\Client\Provider\Google(
            [
                'clientId'     => $this->provider->getSetting('clientId'),
                'clientSecret' => $this->provider->getSetting('clientSecret'),
                'redirectUri'  => $url,
                'hostedDomain' => $parts['scheme'] . '://' . $parts['host'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \League\OAuth2\Client\Provider\GoogleUser) {
            /** @var \League\OAuth2\Client\Provider\GoogleUser  $resource */
            $user->name       = $resource->getName() ?: null;
            $user->firstName  = $resource->getFirstName() ?: null;
            $user->lastName   = $resource->getLastName() ?: null;
            $user->email      = $resource->getEmail() ?: null;
            $user->accountURL = 'https://plus.google.com/' . $resource->getId();
            $user->avatarURL  = $resource->getAvatar() ?: null;
        }

        return $user;
    }
}
