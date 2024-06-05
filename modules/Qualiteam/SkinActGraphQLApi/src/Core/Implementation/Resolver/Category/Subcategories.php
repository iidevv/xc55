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

/**
 * Class Catalog
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product
 */
class Subcategories implements ResolverInterface
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
        $args = $this->prepareSubsetParams($args);

        /** @var \XLite\Model\Category $category */
        $category = $val->categoryModel;

        $subcategories = \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->getSubcategoriesForMobileAPI(
                $category->getCategoryId(),
                $args['from'],
                $args['size']
            );

        if (!$subcategories) {
            return [];
        }

        if ($subcategories instanceof Collection) {
            $subcategories = $subcategories->toArray();
        }

        $mapped = array_map(function ($category) {
            return $this->mapper->mapToDto($category);
        }, $subcategories);

        return $mapped;
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
