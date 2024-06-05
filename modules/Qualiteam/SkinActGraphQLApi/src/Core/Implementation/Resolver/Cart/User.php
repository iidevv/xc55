<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

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
        $user = $val->user;

        if (!$user) {
            return null;
        }

        return $this->mapper->mapToDto($user, $context);
    }
}
