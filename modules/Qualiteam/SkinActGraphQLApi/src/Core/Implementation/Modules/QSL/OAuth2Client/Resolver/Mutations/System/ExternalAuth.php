<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\OAuth2Client\Resolver\Mutations\System;

use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use League\OAuth2\Client\Token\AccessToken;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System\DeviceHandlerTrait;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use Qualiteam\SkinActGraphQLApi\Model\Device;
use QSL\OAuth2Client\Controller\Customer\Oauth2return;
use QSL\OAuth2Client\Model\Provider;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\OAuth2Client")
 *
 */

class ExternalAuth extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System\ExternalAuth
{
    /**
     * {@inheritdoc}
     * @param XCartContext $context
     *
     * @throws Service\AuthException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if (!isset($args['auth']['provider']) || !isset($args['auth']['access_token'])) {
            throw new Service\Auth\InvalidExternalAuthData();
        }

        $profile = $this->getUserProfile($args['auth']['provider'], $args['auth']['access_token']);

        if (!$profile) {
            throw new Service\Auth\AccessDenied();
        }

        /** @var Device $device */
        $device = null;

        if (isset($args['client'])) {
            $device = $this->registerDeviceData($args['client'], $profile);
        }

        return $context->getAuthService()->generateToken($profile, $device);
    }

    /**
     * @param $providerName
     * @param $accessToken
     *
     * @return mixed
     */
    protected function getUserProfile($providerName, $accessToken)
    {
        try {
            $provider = $this->getProvider($providerName);
            $token = new AccessToken($accessToken);
            $result = $provider->getWrapper()->getExternalUserProfile($token);

            $controller = new Oauth2return();
            return $controller->processUserFromApi($result, $providerName);
        } catch (\Exception $e) {
            throw new Service\AuthException($e->getMessage());
        }
    }

    /**
     * @param string $serviceName
     *
     * @return Provider|null
     */
    protected function getProvider($serviceName)
    {
        return $serviceName
            ? \XLite\Core\Database::getRepo('\QSL\OAuth2Client\Model\Provider')->findOneBy(array('service_name' => $serviceName))
            : null;
    }
}
