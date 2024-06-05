<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class Countries
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class Countries implements ResolverInterface
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
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Country');

        $models = $repo->findAllEnabled();

        if ($models instanceof Collection) {
            $models = $models->toArray();
        }

        $mapped = array_map(function ($model) {
            return static::mapToDto($model);
        }, $models);

        return $mapped;
    }

    /**
     * @param \XLite\Model\Country $model
     *
     * @return array
     */
    public static function mapToDto($model)
    {
        return [
            'model'  => $model,
            'code'   => $model->getCode(),
            'name'   => $model->getCountry(),
            'states' => new CountryStates(),
        ];
    }
}
