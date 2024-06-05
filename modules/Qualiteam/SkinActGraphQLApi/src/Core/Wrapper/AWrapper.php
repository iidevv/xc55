<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Wrapper;

use Exception;
use League\OAuth2\Client\Token\AccessToken;
use XLite\Core\Session;
use QSL\OAuth2Client\Core\Transport\Result;
use QSL\OAuth2Client\Core\Transport\User;

/**
 * Abstract OAuth2 wrapper
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

abstract class AWrapper extends \QSL\OAuth2Client\Core\Wrapper\AWrapper
{
    /**
     * Process return
     *
     * @return \QSL\OAuth2Client\Core\Transport\Result
     */
    public function processReturn()
    {
        $result = parent::processReturn();

        if ($result->result !== static::RETURN_SUCCESS) {
            Session::getInstance()->oauth2event = [
                'success' => false,
                'profile_id' => null,
                'token' => null,
                'message' => $result->result
            ];
        }

        return $result;
    }

    /**
     * @param AccessToken $accessToken
     *
     * @return Result
     * @throws Exception
     */
    public function getExternalUserProfile($accessToken)
    {
        $result = new Result();

        $result->result = static::RETURN_FAIL;
        try {
            $internalProvider = $this->getInternalProvider();
            // We got an access token, let's now get the user's details
            $result->user   = $this->normalizeUser($internalProvider->getResourceOwner($accessToken), $accessToken);
            $result->result = $result->user->id
                ? static::RETURN_SUCCESS
                : static::RETURN_USER_FAIL;
        } catch (\Exception $e) {
            $result->result = static::RETURN_FAIL;
        } catch (\Throwable $e) {
            $result->result = static::RETURN_FAIL;
            throw new Exception('Failed to retrieve user details by using access token', 0, $e);
        }

        return $result;
    }
}
