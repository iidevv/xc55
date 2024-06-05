<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

/**
 * Class Catalog
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product
 */
class Catalog implements ResolverInterface
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

        $firstLevelCategories = $repo->getSubcategoriesForMobileAPI(
            $repo->getRootCategoryId()
        );

        $mapped = array_map(function ($category) {
            return $this->mapper->mapToDto($category);
        }, $firstLevelCategories);

        return $mapped;
    }
}
