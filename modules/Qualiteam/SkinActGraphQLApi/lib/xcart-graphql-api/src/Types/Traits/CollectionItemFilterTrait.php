<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Traits;


use XcartGraphqlApi\Types;

trait CollectionItemFilterTrait
{
    public function defineCommonFields()
    {
        return [
            'searchFilter'  => [
                'type'        => Types::string(),
                'description' => 'Search query string',
            ],
            'enabled'       => [
                'type'        => Types::boolean(),
                'description' => 'Search enabled items',
            ]
        ];
    }
}