<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

/**
 * Odnoklassniki wrapper
 */
class Odnoklassniki extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
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
            'clientPublic'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application public key',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'clientSecret'    => [
                \XLite\View\Model\AModel::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application secret key',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Authorization callback URL',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \QSL\OAuth2Client\Core\Override\Odnoklassniki(
            [
                'clientId'     => $this->provider->getSetting('clientId'),
                'clientSecret' => $this->provider->getSetting('clientSecret'),
                'clientPublic' => $this->provider->getSetting('clientPublic'),
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
        if ($resource instanceof \Aego\OAuth2\Client\Provider\OdnoklassnikiResourceOwner) {
            /** @var \Aego\OAuth2\Client\Provider\OdnoklassnikiResourceOwner $resource */
            $user->name       = $resource->getName();
            $user->firstName  = $resource->getFirstName() ?: null;
            $user->lastName   = $resource->getLastName() ?: null;
            $user->avatarURL  = $resource->getImageUrl();
            $user->accountURL = 'https://ok.ru/profile/' . $resource->getId();
            $user->gender     = $resource->getGender() ?: null;
            $user->locale     = $resource->getLocale() ?: null;

            $data = $resource->toArray();
            if (!empty($data['email'])) {
                $user->email = $data['email'];
            }
        }

        return $user;
    }
}
