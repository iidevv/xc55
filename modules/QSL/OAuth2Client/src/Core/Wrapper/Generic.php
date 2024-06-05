<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Generic wrapper
 */
class Generic extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
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
            'urlAuthorize'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Authorize URL',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'urlAccessToken'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Access token URL',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'urlResourceOwnerDetails'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Resource owner details URLS',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Authorized Redirect URL',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \QSL\OAuth2Client\Core\Override\GenericProvider(
            [
                'clientId'                => $this->provider->getSetting('clientId'),
                'clientSecret'            => $this->provider->getSetting('clientSecret'),
                'redirectUri'             => $this->getRedirectURL(),
                'urlAuthorize'            => $this->provider->getSetting('urlAuthorize'),
                'urlAccessToken'          => $this->provider->getSetting('urlAccessToken'),
                'urlResourceOwnerDetails' => $this->provider->getSetting('urlResourceOwnerDetails'),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \League\OAuth2\Client\Provider\GenericResourceOwner) {
            /** @var \League\OAuth2\Client\Provider\GenericResourceOwner $resource */
            $data = $resource->toArray();
            if (!$user->id) {
                $user->id = md5(serialize($data));
            }
            if (!empty($data['email'])) {
                $user->email = $data['email'];
            }
            if (!empty($data['name'])) {
                $user->name = $data['name'];
            }
            if (!empty($data['url'])) {
                $user->accountURL = $data['url'];
            }
        }

        return $user;
    }
}
