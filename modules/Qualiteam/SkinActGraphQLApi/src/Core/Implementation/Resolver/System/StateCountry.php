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
 * Class Countries
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class StateCountry implements ResolverInterface
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
        if (isset($val['country']) && $val['country'] instanceof \XLite\Model\Country) {
            return $this->mapToDto($val['country']);
        }

        throw new \RuntimeException('Can\'t resolve country, not instance of Model\Country');
    }

    /**
     * @param \XLite\Model\Country $model
     *
     * @return array
     */
    protected function mapToDto($model)
    {
        return [
            'code' => $model->getCode(),
            'name' => $model->getCountry(),
        ];
    }
}
