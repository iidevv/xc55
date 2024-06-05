<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Core\Wrapper;

use XLite\InjectLoggerTrait;

/**
 * Microsoft wrapper
 */
class Microsoft extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    use InjectLoggerTrait;

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
                \XLite\View\Model\AModel::SCHEMA_LABEL    => 'Application Secret',
                \XLite\View\Model\AModel::SCHEMA_REQUIRED => true,
            ],
            'authCallbackURL' => [
                \XLite\View\Model\AModel::SCHEMA_CLASS => 'QSL\OAuth2Client\View\FormField\Label\URL',
                \XLite\View\Model\AModel::SCHEMA_LABEL => 'Redirect URI',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function defineInternalProvider()
    {
        return new \QSL\OAuth2Client\Core\Override\Microsoft(
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
            'scope' => ['wl.basic', 'wl.signin', 'wl.emails'],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function normalizeUser(\League\OAuth2\Client\Provider\ResourceOwnerInterface $resource, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = parent::normalizeUser($resource, $token);
        if ($resource instanceof \Stevenmaguire\OAuth2\Client\Provider\MicrosoftResourceOwner) {
            /** @var \Stevenmaguire\OAuth2\Client\Provider\MicrosoftResourceOwner $resource */
            $user->email      = $resource->getEmail() ?: null;
            $user->name       = $resource->getName() ?: null;
            $user->firstName  = $resource->getFirstname() ?: null;
            $user->lastName   = $resource->getLastname() ?: null;
            $user->avatarURL  = $resource->getImageurl();
            $user->accountURL = $resource->getUrls();
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
            true,
            [],
            null,
            false
        );
    }

    /**
     * Check - return is valid or not
     *
     * @return boolean
     */
    protected function isReturnValid()
    {
        $result = parent::isReturnValid();
        if ($result && \XLite\Core\Request::getInstance()->error_description) {
            $result = false;

            $this->getLogger('QSL-OAuth2Client')->error('', [
                'Error'       => \XLite\Core\Request::getInstance()->error,
                'Description' => \XLite\Core\Request::getInstance()->error_description,
                'Provider'    => 'microsoft',
            ]);
        }

        return $result;
    }
}
