<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Zendesk wrapper
 */
class Zendesk extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId'        => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Unique Identifier',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'subdomain'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Subdomain',
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
        return new \Stevenmaguire\OAuth2\Client\Provider\Zendesk(
            [
                'clientId'     => $this->provider->getSetting('clientId'),
                'clientSecret' => $this->provider->getSetting('clientSecret'),
                'subdomain'    => $this->provider->getSetting('subdomain'),
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
                'scope' => ['read'],
            ];
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \Stevenmaguire\OAuth2\Client\Provider\ZendeskResourceOwner) {
            /** @var \Stevenmaguire\OAuth2\Client\Provider\ZendeskResourceOwner $resource */
            $data = $resource->toArray();
            $user->id         = $data['user']['id'];
            $user->name       = $data['user']['name'];
            $user->email      = $data['user']['email'];
            $user->locale     = $data['user']['locale'];
            $user->accountURL = 'https://' . $this->provider->getSetting('subdomain') . '.zendesk.com/agent/users/' . $user->id;

            if (!empty($data['user']['photo'])) {
                $user->avatarURL = $data['user']['photo']['content_url'];
            }
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
