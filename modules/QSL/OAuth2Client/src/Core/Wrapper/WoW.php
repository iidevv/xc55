<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Battlenet : WoW wrapper
 */
class WoW extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * @inheritdoc
     */
    public function getFormFields()
    {
        return [
            'clientId' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Key',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'region' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'QSL\OAuth2Client\View\FormField\Select\WoWRegion',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Region',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Allowed Callback URL',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \Depotwarehouse\OAuth2\Client\Provider\WowProvider(
            [
                'clientId'     => $this->provider->getSetting('clientId'),
                'clientSecret' => $this->provider->getSetting('clientSecret'),
                'redirectUri'  => $this->getRedirectURL(),
                'region'       => $this->provider->getSetting('region'),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \Depotwarehouse\OAuth2\Client\Entity\WowUser) {
            /** @var \Depotwarehouse\OAuth2\Client\Entity\WowUser $resource */
            $data = $resource->toArray();
            $user->name       = $data[0]['name'];
        }

        return $user;
    }
}
