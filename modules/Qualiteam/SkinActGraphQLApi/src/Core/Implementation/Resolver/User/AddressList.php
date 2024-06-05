<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\User;

use XLite\Model\Profile;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

class AddressList implements ResolverInterface
{
    /**
     * @var Mapper\Address
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\Address $mapper
     */
    public function __construct(Mapper\Address $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        /** @var Profile $profile */
        $profile = $val->profileModel;

        if (!$profile) {
            return [];
        }

        return array_map(function($item) {
            return $this->mapper->mapToDto($item);
        }, $profile->getAddresses()->toArray());
    }
}
