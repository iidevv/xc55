<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service;
use Qualiteam\SkinActGraphQLApi\Model\Device;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

class Auth implements ResolverInterface
{
    use DeviceHandlerTrait;

    /**
     * {@inheritdoc}
     * @param XCartContext $context
     *
     * @throws Service\AuthException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $profile = $context->getAuthService()->login($args['auth']['login'], $args['auth']['password']);

        if (!is_object($profile)) {
            switch ($profile) {
                case \XLite\Core\Auth::RESULT_PASSWORD_NOT_EQUAL:
                    throw new Service\Auth\InvalidAuthData();
                    break;

                case \XLite\Core\Auth::MAX_COUNT_OF_LOGIN_ATTEMPTS:
                    throw new Service\Auth\MaxLoginAttempts();
                    break;

                case \XLite\Core\Auth::RESULT_LOGIN_IS_LOCKED:
                    throw new Service\Auth\ProfileIsLocked();
                    break;

                case \XLite\Core\Auth::RESULT_PROFILE_IS_ANONYMOUS:
                    throw new Service\Auth\ProfileIsAnonymous();
                    break;

                default:
                    throw new Service\Auth\AccessDenied();
                    break;
            }
        }

        /** @var Device $device */
        $device = null;

        if (isset($args['client'])) {
            $device = $this->registerDeviceData($args['client'], $profile);
        }

        return $context->getAuthService()->generateToken($profile, $device);
    }
}
