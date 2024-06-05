<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Facebook wrapper
 */
class Facebook extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * Facebook Graph API version
     */
    public const GRAPH_API_VERSION = 'v2.10';

    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application ID',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'enableBetaTier' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOffWithoutOffLabel',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Enable Beta tier',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \League\OAuth2\Client\Provider\Facebook(
            [
                'clientId'        => $this->provider->getSetting('clientId'),
                'clientSecret'    => $this->provider->getSetting('clientSecret'),
                'redirectUri'     => $this->getRedirectURL(),
                'graphApiVersion' => static::GRAPH_API_VERSION,
                'enableBetaTier'  => (bool)$this->provider->getSetting('enableBetaTier'),
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
                'scope' => ['email'],
            ];
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \League\OAuth2\Client\Provider\FacebookUser) {
            /** @var \League\OAuth2\Client\Provider\FacebookUser $resource */
            $user->name       = $resource->getName() ?: null;
            $user->email      = $resource->getEmail() ?: null;
            $user->firstName  = $resource->getFirstName() ?: null;
            $user->lastName   = $resource->getLastName() ?: null;
            $user->avatarURL  = $resource->getPictureUrl() ?: null;
            $user->gender     = $resource->getGender() ?: null;
            $user->locale     = $resource->getLocale() ?: null;
            $user->accountURL = $resource->getLink() ?: null;
            $user->timezone   = $resource->getTimezone() ?: null;
        }

        return $user;
    }
}
