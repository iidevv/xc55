<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use Includes\Utils\Module\Manager;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XcartGraphqlApi\Types\Model\AppData\HomePageWidgetType;
use XLite\Core\CommonCell;
use XLite\Core\Translation;

/**
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class ExternalAuthProviders implements ResolverInterface
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
        return [];
    }

    ///**
    // * @param string $name
    // *
    // * @return bool
    // */
    //protected static function isModuleEnabled($name)
    //{
    //    return Manager::getRegistry()->isModuleEnabled($name);
    //}
}
