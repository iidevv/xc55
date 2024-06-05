<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use XcartGraphqlApi\ResolverFactoryInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use \XLite\Model\Repo;

/**
 * Class Products
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product
 */
class Collection implements ResolverInterface
{
    /**
     * @var ResolverFactoryInterface
     */
    private $factory;

    /**
     * Collection constructor.
     *
     * @param ResolverFactoryInterface $factory
     */
    public function __construct($factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $objResolver = $this->factory->createForType($args['type']);
        $countResolver = $this->factory->createForType($args['type'] . "Count");

        return [
            'count' => $countResolver($val, $args, $context, $info),
            'objects' => $objResolver($val, $args, $context, $info)
        ];
    }
}
