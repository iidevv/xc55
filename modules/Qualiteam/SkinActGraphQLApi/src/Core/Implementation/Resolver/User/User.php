<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\User;

use GraphQL\Error\UserError;
use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class User
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\User
 */
class User implements ResolverInterface
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
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $id = null;

        if (isset($args['id'])) {
            $id = $args['id'];
        } elseif ($context->isAuthenticated() && $context->getLoggedProfile()) {
            $id = $context->getLoggedProfile()->getProfileId();
        }

        /** @var XCartContext $context */
        if (!$this->hasAccessToId($context, $id)) {
            throw new UserError("Can't access {$id}");
        }

        $repo = \XLite\Core\Database::getRepo(Profile::class);

        /** @var Profile $model */
        $model = $repo->find($id);

        if (!$model) {
            throw new UserError("There is no model with {$id} id");
        }

        return $this->mapper->mapToDto($model, $context);
    }

    /**
     * @param XCartContext $context
     * @param string $id
     *
     * @return bool
     */
    protected function hasAccessToId($context, $id)
    {
        return $context->hasAdminAccess()
            || ($context->getLoggedProfile()
                && $context->getLoggedProfile()->getProfileId() === (int) $id
            );
    }
}
