<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Gitlab wrapper
 */
class Gitlab extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId'        => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application Id',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'domain'         => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Optional base URL for self-hosted',
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Callback url',
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

        return new \Omines\OAuth2\Client\Provider\Gitlab($options);
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \Omines\OAuth2\Client\Provider\GitlabResourceOwner) {
            /** @var \Omines\OAuth2\Client\Provider\GitlabResourceOwner $resource */
            $user->name       = $resource->getName() ?: null;
            $user->email      = $resource->getEmail() ?: null;
            $user->avatarURL  = $resource->getAvatarUrl() ?: null;
            $user->accountURL = $resource->getProfileUrl() ?: null;
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
            \XLite\Core\Config::getInstance()->Security->customer_security,
            [],
            null,
            false
        );
    }
}
