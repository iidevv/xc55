<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class TooltipType extends ObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'tooltip',
            'description' => 'Tooltip model',
            'fields'      => function () {
                return [
                    'name'    => Types::string(),
                    'title'   => Types::string(),
                    'tooltip' => Types::string(),
                ];
            },
        ];
    }
}