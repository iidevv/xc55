<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Salesforce wrapper
 */
class Salesforce extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId'        => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Consumer Key',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Consumer Secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'domain'         => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Optional base URL for self-hosted',
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Return URL',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        $options = [
            'clientId'     => $this->provider->getSetting('clientId'),
            'clientSecret' => $this->provider->getSetting('clientSecret'),
            'redirectUri'  => $this->getRedirectURL(),
        ];
        if ($this->provider->getSetting('domain')) {
            $options['domain'] = $this->provider->getSetting('domain');
        }

        return new \Stevenmaguire\OAuth2\Client\Provider\Salesforce($options);
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \Stevenmaguire\OAuth2\Client\Provider\SalesforceResourceOwner) {
            /** @var \Stevenmaguire\OAuth2\Client\Provider\SalesforceResourceOwner $resource */
            $user->email     = $resource->getEmail() ?: null;
            $user->firstName = $resource->getFirstName() ?: null;
            $user->lastName  = $resource->getLastName() ?: null;

            $data = $resource->toArray();
            if (!empty($data['urls']['profile'])) {
                $user->accountURL = $data['urls']['profile'];
            }
            if (!empty($data['photos']['picture'])) {
                $user->avatarURL = $data['photos']['picture'];
            }
            if (!empty($data['locale'])) {
                $user->locale = $data['locale'];
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
