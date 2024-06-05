<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\NoModule;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

class ExternalAuth implements ResolverInterface
{
    use DeviceHandlerTrait;

    /**
     * {@inheritdoc}
     * @param XCartContext $context
     *
     * @throws Service\AuthException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        throw new NoModule();
    }
}
