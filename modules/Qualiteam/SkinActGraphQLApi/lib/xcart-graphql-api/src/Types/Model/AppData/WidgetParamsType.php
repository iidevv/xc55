<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\AppData;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class ProductSelectionType
 */
class WidgetParamsType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'widgetParams',
            'description' => 'widget params data',
            'fields'      => function () {
                return [
                    'filters' => [
                        'type' => Types::string(),
                        'description' => 'JSON-encoded object for use in products query'
                    ]
                ];
            },
        ];
    }
}
