<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Yandex wrapper
 */
class Yandex extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId'        => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application ID',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application password',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Callback URL',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \Aego\OAuth2\Client\Provider\Yandex(
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
        if ($resource instanceof \Aego\OAuth2\Client\Provider\YandexResourceOwner) {
            /** @var \Aego\OAuth2\Client\Provider\YandexResourceOwner $resource */
            $user->name      = $resource->getName() ?: null;
            $user->firstName = $resource->getFirstName() ?: null;
            $user->lastName  = $resource->getLastName() ?: null;
            $user->email     = $resource->getEmail() ?: null;
        }

        return $user;
    }
}
