<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use XLite\Model\Repo;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

class Categories implements ResolverInterface
{
    /**
     * @var Mapper\Category
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\Category $mapper
     */
    public function __construct(Mapper\Category $mapper)
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
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Category');

        $args = $this->prepareArgs($args);
        $cnd = $this->prepareSearchCaseBySearchParams($args);

        $categories = $repo->search($cnd);

        return array_map(
            function ($item) {
                return $this->mapper->mapToDto($item);
            },
            $categories
        );
    }


    /**
     * @param $args
     *
     * @return array
     */
    protected function prepareArgs($args) {
        $defaults = [
            'enabled' => true
        ];

        return array_replace($defaults, $args);
    }

    /**
     * @param $args
     *
     * @return \XLite\Core\CommonCell
     */
    protected function prepareSearchCaseBySearchParams($args)
    {
        $cnd = new \XLite\Core\CommonCell();

        if (isset($args['parent_id']) && $args['parent_id'] > 0) {
            $cnd->{\XLite\Model\Repo\Category::SEARCH_PARENT} = $args['parent_id'];
        }

        if (isset($args['search'])) {
            $cnd->substring = \XLite\Model\SearchCondition\Expression\TypeLike::create(
                'translations.name',
                $args['search']
            );
        }

        if (isset($args['enabled'])) {
            $cnd->enabled = \XLite\Model\SearchCondition\Expression\TypeEquality::create(
                'enabled',
                $args['enabled']
            );
        }

        $from = isset($args['from'])
            ? (int) $args['from']
            : 0;
        $size = isset($args['size'])
            ? (int) $args['size']
            : 0;

        // $size = 0 means without limit
        if ($from || $size) {
            $cnd->{Repo\Category::P_LIMIT} = [ $from, $size];
        }

        $cnd->{\XLite\Model\Repo\Category::P_ORDER_BY} = [
            'c.pos',
            'asc'
        ];
        return $cnd;
    }
}
