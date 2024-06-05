<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service\Register\AlreadyExists;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service\Register\PasswordsDoNotMatch;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Model\Profile;

/**
 * Class UpdateUser
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System
 */
class UpdateUser implements ResolverInterface
{
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
     * @param                  $val
     * @param                  $args
     * @param XCartContext     $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if (
            empty($args['currentPassword'])
            || !$context->isAuthenticated()
            || !$context->getLoggedProfile()
        ) {
            throw new AccessDenied();
        }

        $data = $args['data'];

        if (isset($data['login'])) {
            $this->updateLogin($data['login'], $args['currentPassword'], $context);
        }

        if (isset($data['password'])) {
            $this->updatePassword($data, $args['currentPassword'], $context);
        }

        return $this->mapper->mapToDto($context->getLoggedProfile(), $context);
    }

    /**
     * @param string $login
     * @param string $currentPassword
     * @param XCartContext $context
     */
    protected function updateLogin($login, $currentPassword, $context)
    {
        [$profile, $result] = Auth::getInstance()->checkLoginPassword(
            $context->getLoggedProfile()->getLogin(),
            $currentPassword
        );

        if (!is_object($profile) || $result !== true) {
            throw new AccessDenied();
        }

        /** @var \XLite\Model\Repo\Profile $repo */
        $repo = Database::getRepo(Profile::class);
        if ($login) {
            $profileFound = $repo->findOneByLogin($login);

            if (
                $profileFound
                && $profileFound->getProfileId() !== $profile->getProfileId()
            ) {
                throw new AlreadyExists($login);
            }
        }

        if ($login) {
            $context->getLoggedProfile()->setLogin($login);
            $profile->setLogin($login);
        }

        Database::getEM()->flush();
    }

    /**
     * @param array        $data
     * @param string       $currentPassword
     * @param XCartContext $context
     */
    protected function updatePassword($data, $currentPassword, $context)
    {
        /** @var \XLite\Model\Profile $profile */
        [$profile, $result] = Auth::getInstance()->checkLoginPassword(
            $context->getLoggedProfile()->getLogin(),
            $currentPassword
        );

        if (!is_object($profile) || $result !== true || $context->getLoggedProfile() !== $profile) {
            throw new AccessDenied();
        }

        if (!empty($data['password']) && $data['password'] !== $data['password_conf']) {
            throw new PasswordsDoNotMatch();
        }

        $profile->setPassword(
            Auth::encryptPassword($data['password'])
        );

        Database::getEM()->flush();
    }
}
