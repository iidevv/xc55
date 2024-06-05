<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Authenticator;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception;

/**
 * Interface ResolverInterface
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Authenticator
 */
interface AuthenticatorInterface
{
    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return void
     *
     * @throws Exception\AccessDenied
     */
    public function auth($val, $args, ContextInterface $context, ResolveInfo $info);
}
