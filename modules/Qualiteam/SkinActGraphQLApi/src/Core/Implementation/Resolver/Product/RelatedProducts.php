<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

class RelatedProducts implements ResolverInterface
{
    /**
     * @var Mapper\Product
     */
    protected $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\Product $mapper
     */
    public function __construct(Mapper\Product $mapper)
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
        $id = (int) $args['id'];

        $products = \XLite\Core\Database::getRepo('\XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts($id);

        return array_map(
            function ($product) {
                return $this->mapper->mapToDto($product->product);
            },
            $products
        );
    }
}
