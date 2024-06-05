<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;

/**
 * Interface ResolverInterface
 * @package XcartGraphqlApi\Resolver
 */
interface ResolverInterface
{
    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __invoke($val, $args, $context, ResolveInfo $info);
}
