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
 * Class ProfileFieldType
 */
class ProfileFieldType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'profile_field',
            'description' => 'Profile field (aka addressField) model',
            'fields'      => function () {
                return [
                    'service_name'  => Types::string(),
                    'type'          => Types::string(),
                    'name'          => Types::string(),
                    'placeholder'   => Types::string(),
                    'required'      => Types::boolean(),
                ];
            },
        ];
    }
}
