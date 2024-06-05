<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category;

use GraphQL\Error\UserError;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class Category
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product
 */
class Category implements ResolverInterface
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

        $id = (int)$args['id'];
        if ($id === 0) {
            $id = $repo->getRootCategoryId();
        }

        $model = $repo->find($id);

        if (!$model) {
            throw new UserError("There is no model with {$args['id']} id");
        }

        if (!$model->getEnabled()) {
            throw new UserError("Model with {$args['id']} id is not accessible");
        }

        $dto = $this->mapper->mapToDto($model);

        $dto->subcategories = [];
        $dto->products = [];

        return $dto;
    }
}
