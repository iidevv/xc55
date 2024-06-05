<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Customer;

use XLite\Core\Database;
use XLite\Core\Session;
use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\AuthService;
use QSL\OAuth2Client\Core\Transport\Result;

/**
 * Return controller
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\OAuth2Client")
 *
 */

class Oauth2return extends \QSL\OAuth2Client\Controller\Customer\Oauth2return
{
    /**
     * Process user
     *
     * @param Result $authResult Result
     *
     * @return string
     */
    protected function processUser(Result $authResult)
    {
        $result = parent::processUser($authResult);

        if ($result && Session::getInstance()->profile_id) {
            Session::getInstance()->oauth2event = [
                'success' => ($result !== static::PROCESS_USER_NOT_FOUND),
                'profile_id' => Session::getInstance()->profile_id,
                'token' => $this->getGraphQLToken(),
                'message' => $result
            ];
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getGraphQLToken()
    {
        $profile = Database::getRepo('XLite\Model\Profile')->find(Session::getInstance()->profile_id);
        $authService = new AuthService();

        return $profile
            ? $authService->generateToken($profile)
            : null;
    }

    /**
     * @param Result $authResult
     * @param string $provider
     *
     * @return Profile|null
     * @throws \Exception
     */
    public function processUserFromApi($authResult, $provider)
    {
        \XLite\Core\Request::getInstance()->provider = $provider;
        $result = $this->processUser($authResult);
        $profile = null;

        switch ($result) {
            case static::PROCESS_USER_ASSOCIATED:
            case static::PROCESS_USER_CREATED:
            case static::PROCESS_USER_LINKED:
            case static::PROCESS_USER_RELINKED:
            case static::PROCESS_USER_ALREADY_LINKED:
            case static::PROCESS_LOGGED:
                $profileId = Session::getInstance()->profile_id;
                $profile = Database::getRepo('XLite\Model\Profile')->find($profileId);
                break;

            case static::PROCESS_USER_NOT_FOUND:
            default:
                throw new \Exception('No user with such an external profile has been found');
                break;
        }

        \XLite\Core\Database::getEM()->flush();

        return $profile;
    }
}