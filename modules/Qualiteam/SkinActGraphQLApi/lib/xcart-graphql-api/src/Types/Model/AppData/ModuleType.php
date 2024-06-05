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
 * Class ModuleType
 */
class ModuleType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'module',
            'description' => 'Module model',
            'fields'      => function () {
                return [
                    'name'    => Types::string(),
                    'enabled' => Types::boolean(),
                ];
            },
        ];
    }
}
