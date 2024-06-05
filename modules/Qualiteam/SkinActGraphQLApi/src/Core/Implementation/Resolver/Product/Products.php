<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use XLite\Model\Repo;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

class Products implements ResolverInterface
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
        $args = $this->prepareArgs($args);
        $cnd = $this->prepareSearchCaseBySearchParams($args);

        $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd);

        return array_map(
            function ($product) {
                return $this->mapper->mapToDto($product);
            },
            $products
        );
    }

    /**
     * @param $args
     *
     * @return array
     */
    protected function prepareArgs($args) {
        $defaults = [
            'filters' => [
                'enabled' => true,
                'stock' => true,
            ],
        ];

        $result = array_replace($defaults, $args);

        if (isset($args['filters'])) {
            $result['filters'] = array_replace(
                $defaults['filters'],
                isset($args['filters']) ? $args['filters'] : []
            );
        }
        return $result;
    }

    /**
     * @param $args
     *
     * @return \XLite\Core\CommonCell
     */
    protected function prepareSearchCaseBySearchParams($args)
    {
        $cnd = new \XLite\Core\CommonCell();

        $this->prepareFilters($cnd, $args['filters']);

        $from = isset($args['from'])
            ? (int) $args['from']
            : 0;
        $size = isset($args['size'])
            ? (int) $args['size']
            : 0;

        // $size = 0 means without limit
        if ($from || $size) {
            $cnd->{Repo\Product::P_LIMIT} = [ $from, $size];
        }

        return $cnd;
    }

    /**
     * @param \XLite\Core\CommonCell $cnd
     * @param array                  $filters
     */
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        if (isset($filters['categoryId']) && $filters['categoryId'] == 0) {
            $filters['categoryId'] = \XLite\Core\Database::getRepo('XLite\Model\Category')
                ->getRootCategoryId();
        }

        if (isset($filters['categoryId'])) {
            $cnd->{Repo\Product::P_CATEGORY_ID} = $filters['categoryId'];

            $cnd->{Repo\Product::P_ORDER_BY} = ['cp.orderby', 'DESC'];
        } else {
            $cnd->{Repo\Product::P_ORDER_BY} = ['p.product_id', 'DESC'];
        }

        if (isset($filters['enabled'])) {
            $cnd->{Repo\Product::P_ENABLED} = $filters['enabled'];
        }

        if (isset($filters['removeEnabledCnd'])) {
            $cnd->{Repo\Product::P_REMOVE_ENABLED_CND} = true;
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
}
