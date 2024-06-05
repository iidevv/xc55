<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class Memberships
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class Memberships implements ResolverInterface
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
        $memberships = \XLite\Core\Database::getRepo('XLite\Model\Membership')->findActiveMemberships();

        $mapped = array_map(function($module) {
            return $this->mapToDto($module);
        }, $memberships);

        array_unshift($mapped, $this->getDefaultValue());

        return $mapped;
    }

    /**
     * @param \XLite\Model\Membership $model
     *
     * @return array
     */
    protected function mapToDto($model)
    {
        return [
            'id'   => $model->getMembershipId(),
            'name' => $model->getName(),
        ];
    }

    /**
     * TODO This shouldn't be a part of the API
     * @return array
     */
    protected function getDefaultValue()
    {
        return [
            'id' => 0,
            'name' => \XLite\Core\Translation::getInstance()->translate('Ignore membership')
        ];
    }
}
