<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\WishList;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\NoModule;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product;

class WishListItems implements ResolverInterface
{
    /**
     * @var Product
     */
    protected $mapper;

    /**
     * WishListItems constructor.
     *
     * @param Product $mapper
     */
    public function __construct(Product $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        throw new NoModule();
    }
}
