<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product;

use GraphQL\Error\UserError;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class Product
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver
 */
class ProductShippingSection implements ResolverInterface
{

    /**
     * @var Mapper\ProductShippingSection
     */
    protected $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\ProductShippingSection $mapper
     */
    public function __construct(Mapper\ProductShippingSection $mapper)
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
        return $this->mapper->mapProductShippingSection($val->productModel);
    }
}
