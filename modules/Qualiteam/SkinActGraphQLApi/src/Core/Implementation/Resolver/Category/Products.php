<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category;

use Doctrine\Common\Collections\Collection;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use \XLite\Model\Repo;

/**
 * Class Products
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product
 */
class Products implements ResolverInterface
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
        $args = $this->prepareSubsetParams($args);
        /** @var \XLite\Model\Category $category */
        $category = $val->categoryModel;

        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Product::P_LIMIT} = [ $args['from'], $args['size']];

        if (isset($args['filters'])) {
            $this->prepareFilters($cnd, array_merge($args['filters'], ['categoryId' => $category->getId()]));
        }

        $products = $category->getProducts($cnd);

        if (!$products) {
            return [];
        }

        if ($products instanceof Collection) {
            $products = $products->toArray();
        }

        $mapped = array_map(function ($product) {
            return $this->mapper->mapToDto($product);
        }, $products);

        return $mapped;
    }

    /**
     * @param \XLite\Core\CommonCell $cnd
     * @param array                  $filters
     */
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        if (isset($filters['categoryId'])) {
            $cnd->{Repo\Product::P_CATEGORY_ID} = $filters['categoryId'];

            $cnd->{Repo\Product::P_ORDER_BY} = ['cp.orderby', 'DESC'];
        } else {
            $cnd->{Repo\Product::P_ORDER_BY} = ['p.product_id', 'DESC'];
        }

        if (isset($filters['enabled'])) {
            $cnd->{Repo\Product::P_ENABLED} = $filters['enabled'];
        }

        if (isset($filters['inSubcats'])) {
            $cnd->{Repo\Product::P_SEARCH_IN_SUBCATS} = true;
        }

        if (
            isset($filters['searchFilter'])
            && !empty($filters['searchFilter'])
        ) {
            $cnd->{Repo\Product::P_SUBSTRING} = $filters['searchFilter'];
            $cnd->{Repo\Product::P_BY_TITLE} = true;
        }

        if (isset($filters['orderByFilter'])) {
            $cnd->{Repo\Product::P_ORDER_BY} = array(
                $filters['orderByFilter']['name'],
                $filters['orderByFilter']['order'],
            );
        }

        if (isset($filters['priceFilter'])) {
            $value = $filters['priceFilter'];

            $cnd->{\XLite\Model\Repo\Product::P_PRICE} = [
                $value['from'],
                $value['to']
            ];
        }

        if (isset($filters['stockFilter'])) {
            $mappedName = $filters['stockFilter'] ? \XLite\Model\Repo\Product::INV_IN : \XLite\Model\Repo\Product::INV_LOW;

            if ($mappedName) {
                $cnd->{\XLite\Model\Repo\Product::P_INVENTORY} = $mappedName;
            }
        }
    }

    /**
     * @param array $args
     *
     * @return array
     */
    protected function prepareSubsetParams($args)
    {
        return array_replace(
            [ 'from' => 0, 'size' => 0 ],
            $args
        );
    }
}
