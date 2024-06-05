<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Paypal wrapper
 */
class Paypal extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
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
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'isSandbox'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Use sandbox',
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
        return new \Stevenmaguire\OAuth2\Client\Provider\Paypal(
            [
                'clientId'     => $this->provider->getSetting('clientId'),
                'clientSecret' => $this->provider->getSetting('clientSecret'),
                'isSandbox'    => $this->provider->getSetting('isSandbox'),
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
            'scope' => ['openid', 'profile', 'email', 'address'],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \Stevenmaguire\OAuth2\Client\Provider\PaypalResourceOwner) {
            /** @var \Stevenmaguire\OAuth2\Client\Provider\PaypalResourceOwner $resource */
            $user->email  = $resource->getEmail() ?: null;
            $user->name   = $resource->getName() ?: null;
            $user->gender = $resource->getGender() ?: null;
            $user->locale = $resource->getLocale() ?: null;
            $user->firstName = $resource->getGivenName() ?: null;
            $user->lastName = $resource->getFamilyName() ?: null;
        }

        return $user;
    }
}
