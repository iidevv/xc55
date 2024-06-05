<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Yahoo wrapper
 */
class Yahoo extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
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
            'authCallbackDomain' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Callback Domain',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \Hayageek\OAuth2\Client\Provider\Yahoo(
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
        if ($resource instanceof \Hayageek\OAuth2\Client\Provider\YahooUser) {
            /** @var \Hayageek\OAuth2\Client\Provider\YahooUser $resource */
            $user->email     = $resource->getEmail() ?: null;
            $user->firstName = $resource->getFirstName() ?: null;
            $user->lastName  = $resource->getLastName() ?: null;
            $user->avatarURL = $resource->getAvatar() ?: null;

            $data = $resource->toArray();
            if (!empty($data['profile']) && !empty($data['profile']['profileUrl'])) {
                $user->accountURL = $data['profile']['profileUrl'];
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
            false,
            [],
            null,
            false
        );
    }

    /**
     * @inheritdoc
     */
    public function isCustomProperty($name)
    {
        return parent::isCustomProperty($name)
            || $name === 'authCallbackDomain';
    }

    /**
     * @inheritdoc
     */
    public function getCustomProperty($name)
    {
        if ($name === 'authCallbackDomain') {
            $url = $this->getRedirectURL();
            $parts = parse_url($url);

            $result = $parts['host'];
        } else {
            $result = parent::getCustomProperty($name);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function isSetting($name)
    {
        return $name !== 'authCallbackDomain' && parent::isSetting($name);
    }
}
