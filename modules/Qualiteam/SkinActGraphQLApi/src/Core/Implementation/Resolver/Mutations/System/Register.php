<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System;

use Qualiteam\SkinActGraphQLApi\Model\Device;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service\Register\AlreadyExists;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service\Register\PasswordsDoNotMatch;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class Register
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System
 */
class Register implements ResolverInterface
{
    use DeviceHandlerTrait;

    /**
     * @var Mapper\User
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\User $mapper
     */
    public function __construct(Mapper\User $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * {@inheritdoc}
     * @param XCartContext $context
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if ($context->isAuthenticated()
            && $context->getLoggedProfile()
            && !$context->getLoggedProfile()->getAnonymous()
        ) {
            throw new AccessDenied();
        }

        $data = $args['data'];
        $data += ['login' => '', 'password' => ''];

        if (empty($data['login']) && isset($data['email'])) {
            $data['login'] = $data['email'];
        }

        if ($data['password'] != $data['password_conf']) {
            throw new PasswordsDoNotMatch();
        }

        $repo = \XLite\Core\Database::getRepo('XLite\Model\Profile');
        if ($data['login'] && $repo->findOneByLogin($data['login'])) {
            throw new AlreadyExists($data['login']);
        }

        $profile = new \XLite\Model\Profile();
        $profile->setLogin($data['login']);
        $profile->setPassword(
            \XLite\Core\Auth::encryptPassword($data['password'])
        );

        \XLite\Core\Database::getEM()->persist($profile);

        /** @var Device $device */
        $device = null;

        if (isset($args['client'])) {
            $device = $this->registerDeviceData($args['client'], $profile);
        }

        \XLite\Core\Database::getEM()->flush();

        $token = $context->getAuthService()->generateToken($profile, $device);

        return [
            'id'   => $profile->getProfileId() . '|' . $token,
            'jwt'  => $token,
            'user' => $this->mapper->mapToDto($profile, $context, $token)
        ];
    }
}
