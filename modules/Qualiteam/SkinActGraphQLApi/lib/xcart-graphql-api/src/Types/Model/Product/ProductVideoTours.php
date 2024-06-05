<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Product;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class ProductVideoTours extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'video_tabs_info',
            'description' => 'Video tours',
            'fields'      => function () {
                return [
                    'video_url' => Types::string(),
                    'description' => Types::string(),
                    'enabled' => Types::boolean(),
                    'position' => Types::int(),
                ];
            },
        ];
    }
}
