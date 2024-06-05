<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Slack wrapper
 */
class Slack extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
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
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Client Secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'team'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Team name',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Redirect URL',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \AdamPaterson\OAuth2\Client\Provider\Slack(
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
                'team'  => $this->provider->getSetting('team'),
                'scope' => ['users:read'],
            ];
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \AdamPaterson\OAuth2\Client\Provider\SlackResourceOwner) {
            /** @var \AdamPaterson\OAuth2\Client\Provider\SlackResourceOwner $resource */
            $user->email      = $resource->getEmail() ?: null;
            $user->firstName  = $resource->getFirstName() ?: null;
            $user->lastName   = $resource->getLastName() ?: null;
            $user->avatarURL  = $resource->getImage192() ?: null;
            $user->accountURL = 'https://' . $this->provider->getSetting('team') . '.slack.com/team/' . $resource->getName();
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getRedirectURL()
    {
        return \XLite\Core\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL('oauth2return', null, ['provider' => $this->provider->getServiceName()], \XLite::CART_SELF),
            true,
            [],
            null,
            false
        );
    }
}
