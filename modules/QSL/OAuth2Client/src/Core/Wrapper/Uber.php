<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Uber wrapper
 */
class Uber extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId'        => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Client id',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'API Secret Key',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Redirect URI',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \Stevenmaguire\OAuth2\Client\Provider\Uber(
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
    protected function getAuthorizationUrlArguments()
    {
        return parent::getAuthorizationUrlArguments()
            + [
                'scope' => ['profile'],
            ];
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \Stevenmaguire\OAuth2\Client\Provider\UberResourceOwner) {
            /** @var \Stevenmaguire\OAuth2\Client\Provider\UberResourceOwner $resource */
            $user->email     = $resource->getEmail() ?: null;
            $user->firstName = $resource->getFirstname() ?: null;
            $user->lastName  = $resource->getLastname() ?: null;
            $user->avatarURL = $resource->getImageurl() ?: null;
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getRedirectURL()
    {
        return \XLite\Core\URLManager::getShopURL(
            \XLite::CART_SELF,
            true,
            [],
            null,
            false
        );
    }
}
