<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Controller\Customer;

use XLite\InjectLoggerTrait;

/**
 * Return controller
 */
class Oauth2return extends \XLite\Controller\Customer\ACustomer
{
    use InjectLoggerTrait;

    /**
     * Process user result codes
     */
    public const PROCESS_USER_RELINKED       = 'relinked';
    public const PROCESS_USER_LINKED         = 'linked';
    public const PROCESS_USER_ALREADY_LINKED = 'already_linked';
    public const PROCESS_USER_ASSOCIATED     = 'associated';
    public const PROCESS_USER_CREATED        = 'created';
    public const PROCESS_LOGGED              = 'logged';
    public const PROCESS_USER_NOT_FOUND      = 'not_found';

    /**
     * @inheritdoc
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && $this->getProvider()
            && $this->getProvider()->getEnabled()
            && $this->getProvider()->getWrapper()->isConfigured();
    }

    /**
     * @inheritdoc
     */
    protected function doNoAction()
    {
        $provider = $this->getProvider();
        $result = $provider->getWrapper()->processReturn();
        $userResult = null;
        switch ($result->result) {
            case \QSL\OAuth2Client\Core\Wrapper\AWrapper::RETURN_INVALID_STATE:
            case \QSL\OAuth2Client\Core\Wrapper\AWrapper::RETURN_USER_FAIL:
            case \QSL\OAuth2Client\Core\Wrapper\AWrapper::RETURN_FAIL:
            case \QSL\OAuth2Client\Core\Wrapper\AWrapper::RETURN_TOKEN_FAIL:
                \XLite\Core\TopMessage::addError('Authentication error. Please contact the store administrator.');
                break;

            case \QSL\OAuth2Client\Core\Wrapper\AWrapper::RETURN_SUCCESS:
                $userResult = $this->processUser($result);
                switch ($userResult) {
                    case static::PROCESS_USER_ALREADY_LINKED:
                        \XLite\Core\TopMessage::addInfo('The external profile is already connected with the account');
                        break;

                    case static::PROCESS_USER_ASSOCIATED:
                        \XLite\Core\TopMessage::addInfo('The external profile is connected to an existing account, and the account has been used for sign-in');
                        break;

                    case static::PROCESS_USER_CREATED:
                        \XLite\Core\TopMessage::addInfo('An account has been created based on the external profile, and the account has been used for sign-in');
                        break;

                    case static::PROCESS_USER_LINKED:
                        \XLite\Core\TopMessage::addInfo('The external profile has been connected to the account successfully');
                        break;

                    case static::PROCESS_USER_NOT_FOUND:
                        \XLite\Core\TopMessage::addWarning('No user with such an external profile has been found');
                        break;

                    case static::PROCESS_USER_RELINKED:
                        \XLite\Core\TopMessage::addInfo('The external profile has replaced another profile that used to be associated with the account');
                        break;

                    default:
                }
                break;

            default:
        }

        parent::doNoAction();

        $data = $provider->getWrapper()->getState();

        $this->getLogger('QSL-OAuth2Client')->debug('OAuth2 return', [
            'Request'                => $_GET,
            'Result'                 => $result->result,
            'Token'                  => $result->token ? $result->token->getToken() : '',
            'User processing result' => $userResult,
            'Provider'               => $provider->getServiceName(),
            'Return URL'             => ($data && !empty($data['returnURL'])) ? $data['returnURL'] : '',
        ]);

        // If return URL is login page - redirect to home page
        if ($data && !empty($data['returnURL']) && preg_match('/target=login/Ss', $data['returnURL'])) {
            unset($data['returnURL']);
        }

        $this->setReturnURL(($data && !empty($data['returnURL'])) ? $data['returnURL'] : $this->getShopURL(static::buildURL()));

        $provider->getWrapper()->unsetState();

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Process user
     *
     * @param \QSL\OAuth2Client\Core\Transport\Result $authResult Result
     *
     * @return string
     */
    protected function processUser(\QSL\OAuth2Client\Core\Transport\Result $authResult)
    {
        $result = null;

        if (!$authResult->user->email && \XLite\Core\Config::getInstance()->QSL->OAuth2Client->allow_user_without_email) {
            $authResult->user->email = $this->getProvider()->getServiceName() . $authResult->user->id . '@example.com';
        }

        if (\XLite\Core\Auth::getInstance()->isLogged()) {
            // Add to logged profile
            $user = \XLite\Core\Auth::getInstance()->getProfile();

            // Try detect and link
            $profile = $user->getExternalProfileByProvider($this->getProvider());
            if (!$profile || $profile->getExternalId() != $authResult->user->id) {
                if ($profile) {
                    $user->removeExternalProfile($profile);
                    $profile->setProfile(null);
                    \XLite\Core\Database::getEM()->remove($profile);
                    $result = static::PROCESS_USER_RELINKED;
                } else {
                    $result = static::PROCESS_USER_LINKED;
                }
                \QSL\OAuth2Client\Model\ExternalProfile::createByProfile($user, $authResult->user, $this->getProvider());
            } else {
                // Already linked
                $result = static::PROCESS_USER_ALREADY_LINKED;
            }
        } else {
            // Try login exists profile

            /** @var \QSL\OAuth2Client\Model\Repo\ExternalProfile $repo */ #nolint
            $repo = \XLite\Core\Database::getRepo('QSL\OAuth2Client\Model\ExternalProfile');

            /** @var \QSL\OAuth2Client\Model\ExternalProfile $profile */
            $profile = $repo->findOneByExternalIdAndProvider($authResult->user->id, $this->getProvider());

            /** @var \XLite\Model\Profile $user */
            $user = null;

            // Try associate with existing user (only customer and if customer has not linked external profile with current provider)
            if (!$profile && \XLite\Core\Config::getInstance()->QSL->OAuth2Client->allow_associate_user && $authResult->user->email) {
                /** @var \XLite\Model\Profile $user */
                $user = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                    ->findByLogin($authResult->user->email);
                if ($user && !$user->isAdmin() && !$repo->isExistsProvider($user, $this->getProvider())) {
                    $profile = \QSL\OAuth2Client\Model\ExternalProfile::createByProfile($user, $authResult->user, $this->getProvider());
                    $result = static::PROCESS_USER_ASSOCIATED;
                }
            }

            // Create new user
            if (
                !$profile
                && !$user
                && \XLite\Core\Config::getInstance()->QSL->OAuth2Client->allow_create_user
                && !\XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($authResult->user->email)
            ) {
                $profile = \QSL\OAuth2Client\Model\ExternalProfile::createBoth($authResult->user, $this->getProvider());
                $result = static::PROCESS_USER_CREATED;
            }

            // Log-in
            if ($profile && \XLite\Core\Auth::getInstance()->loginOAuth2Profile($profile)) {
                if (!$result) {
                    $result = static::PROCESS_LOGGED;
                }
            } else {
                $result = static::PROCESS_USER_NOT_FOUND;
            }
        }

        return $result;
    }

    /**
     * Generate & redirect to external Auth link
     *
     * @return void
     */
    protected function doActionAuth()
    {
        $this->redirect($this->getAuthURL());
    }

    /**
     * Define auth. request URL
     *
     * @return string
     */
    protected function getAuthURL()
    {
        return $this->getProvider()->getWrapper()->getRequestURL($this->getReferrerURL());
    }

    /**
     * Get provider
     *
     * @return \QSL\OAuth2Client\Model\Provider
     */
    protected function getProvider()
    {
        $provider = \XLite\Core\Request::getInstance()->provider;

        return $provider
            ? \XLite\Core\Database::getRepo('QSL\OAuth2Client\Model\Provider')->findOneBy(['service_name' => $provider])
            : null;
    }
}
