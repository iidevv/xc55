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
 * Class Languages
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class Languages implements ResolverInterface
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
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Language');

        $models = $repo->findActiveLanguages();

        $mapped = array_map(function ($model) {
            return static::mapToDto($model);
        }, $models);

        return $mapped;
    }

    /**
     * @param \XLite\Model\Language $model
     *
     * @return array
     */
    public static function mapToDto($model)
    {
        return [
            'code'    => $model->getCode(),
            'name'    => $model->getName(),
        ];
    }
}
