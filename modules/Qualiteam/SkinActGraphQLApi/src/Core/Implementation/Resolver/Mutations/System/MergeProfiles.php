<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

class MergeProfiles implements ResolverInterface
{
    use MergeProfilesTrait;

    /**
     * {@inheritdoc}
     * @param XCartContext $context
     *
     * @throws Service\AuthException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if (!$context->isAuthenticated()
            || !$context->getLoggedProfile() ) {
            throw new AccessDenied();
        }

        if (!isset($args['anonymous_jwt'])) {
            throw new Service\Auth\InvalidMergeWithToken();
        }

        $profile = $context->getLoggedProfile();
        $jwt = $args['anonymous_jwt'];

        return $this->mergeProfileWithToken($context, $profile, $jwt);
    }
}
