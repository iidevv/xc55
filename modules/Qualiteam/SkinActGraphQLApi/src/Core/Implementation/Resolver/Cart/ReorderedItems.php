<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActProductReOrdering\Model\Repo\Product as ReOrderModel;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Product;

class ReorderedItems implements ResolverInterface
{
    /**
     * @var Mapper\Product
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\Product $mapper
     */
    public function __construct(Mapper\Product $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $profile = $context->getLoggedProfile();
        $cnd                                      = new CommonCell();
        $cnd->{ReOrderModel::RE_ORDER_PROFILE_ID} = $profile->getProfileId();

        $products = Database::getRepo(Product::class)->search($cnd);

        return array_map(
            function ($product) use ($profile) {
                $lastItem = $product->getLastOrderItemDB($profile);

                #TODO check of init mapper
                $result = $this->mapper->mapToDto($product);
                
                $result->reorder_attributes = $lastItem->getAttributeValues();
                $result->display_price = $lastItem->getDisplayPrice();

                return $result;
            },
            $products
        );
    }
}