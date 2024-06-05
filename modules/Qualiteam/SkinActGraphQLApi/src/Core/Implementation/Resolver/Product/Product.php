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
class Product implements ResolverInterface
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
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Product');

        /** @var \XLite\Model\Product $model */
        $model = $repo->find($args['id']);

        if ($context->hasCustomerAccess() && !$model->isVisible()) {
            throw new UserError("Model with {$args['id']} id is not accessible");
        }

        if (!$model) {
            throw new UserError("There is no model with {$args['id']} id");
        }

        return $this->mapper->mapToDto($model, $info->getFieldSelection());
    }
}
