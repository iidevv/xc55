<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class ReviewType
 * @package XcartGraphqlApi\Types\Model
 */
class ReviewType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'review',
            'description' => 'Review information model',
            'fields'      => function () {
                return [
                    'id'            => Types::id(),
                    'review'        => Types::string(),
                    'reviewerName'  => Types::string(),
                    'rating'        => Types::int(),
                    'additionDate'  => Types::string(),
                ];
            },
        ];
    }
}
