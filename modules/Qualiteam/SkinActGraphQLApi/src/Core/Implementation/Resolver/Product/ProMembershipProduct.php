<?php


namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product;


use GraphQL\Type\Definition\ResolveInfo;

use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Database;

class ProMembershipProduct implements ResolverInterface
{

    /**
     * @var \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product
     */
    protected $mapper;

    /**
     * Product constructor.
     *
     * @param \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product $mapper
     */
    public function __construct(\Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $products = Database::getRepo('\XLite\Model\Product')->getProMembershipProducts();

        return array_map(
            function ($product) {
                return $this->mapper->mapToDto($product);
            },
            $products
        );
    }
}