<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;


class ColorSwatchesType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'colorSwatches',
            'description' => 'ColorSwatches information model',
            'fields'      => function () {
                return [
                    'color' => Types::string(),
                    'name' => Types::string(),
                ];
            },
        ];
    }
}
