<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\Reviews;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\NoModule;
use Qualiteam\SkinActYotpoReviews\Core\Api\Reviews\CreateBuilder;
use XCart\Container;
use XcartGraphqlApi\Resolver\ResolverInterface;

class Create implements ResolverInterface
{
    private $moduleManagerDomain;

    public function __construct()
    {
        $this->moduleManagerDomain = Container::getContainer()->get('XCart\Domain\ModuleManagerDomain');
    }

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if ($this->moduleManagerDomain->isEnabled('Qualiteam-SkinActYotpoReviews')) {
            $builder = new CreateBuilder($args);
            $result  = $builder->getResult();

            return (bool) $result['success'];
        } else {
            throw new NoModule();
        }
    }
}