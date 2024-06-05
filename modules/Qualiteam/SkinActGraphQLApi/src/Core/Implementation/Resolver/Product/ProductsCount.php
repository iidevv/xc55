<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;

class ProductsCount extends Products
{
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
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Product');
        $args = $this->prepareArgs($args);
        $cnd = $this->prepareSearchCaseBySearchParams($args);

        $count = $repo->search($cnd, $repo::SEARCH_MODE_COUNT);

        return $count;
    }
}
