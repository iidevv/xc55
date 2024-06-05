<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System;

use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service;

trait MergeProfilesTrait
{
    /**
     * @param XCartContext $context
     * @param Profile      $profile
     * @param string       $jwt
     *
     * @return bool
     * @throws \Exception
     */
    public function mergeProfileWithToken($context, $profile, $jwt)
    {
        $token = $context->extractTokenFromJwt($jwt);

        if (!$token
            || !isset($token['user_id'])
            || $token['access'] !== 'anonymous') {
            throw new Service\Auth\InvalidMergeWithToken();
        }

        $mergedProfile = $context->getAuthService()->findProfileById($token['user_id']);
        return $context->getAuthService()->mergeProfiles($profile, $mergedProfile);
    }
}