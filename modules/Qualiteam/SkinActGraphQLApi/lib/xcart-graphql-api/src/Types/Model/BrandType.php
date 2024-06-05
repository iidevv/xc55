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
 * Class BrandType
 * @package XcartGraphqlApi\Types\Model
 */
class BrandType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'brand',
            'description' => 'Brand information model',
            'fields'      => function () {
                return [
                    'id' => Types::id(),
                    'image' => Types::string(),
                    'name' => Types::string(),
                ];
            },
        ];
    }
}
