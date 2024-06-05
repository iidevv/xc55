<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class DealBlockType extends ObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'deal_block',
            'description' => "Today's deal block settings",
            'fields'      => function () {
                return [
                    'sectionName'   => Types::string(),
                    'categoryId'    => Types::int(),
                    'productsCount' => Types::int(),
                ];
            },
        ];
    }
}