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
use Includes\Utils\Module\Module;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class Modules
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class Modules implements ResolverInterface
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
        $modules = Manager::getRegistry()->getModules();

        return array_map(function($module) {
            return $this->mapToDto($module);
        }, $modules);
    }

    /**
     * @param Module $model
     *
     * @return array
     */
    protected function mapToDto($model)
    {
        return [
            'name'    => $model->id,
            'enabled' => $model->isEnabled(),
        ];
    }
}
