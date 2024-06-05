<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use XcartGraphqlApi\Types;

/**
 * Class AppDataType
 * @package XcartGraphqlApi\Types
 */
class ShowroomInfoType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'showroom_info',
            'description' => 'Showroom info data',
            'fields'      => function () {
                return [
                    'title'  => Types::string(),
                    'subtitle'  => Types::string(),
                    'working_hours'  => Types::string(),
                    'address' => Types::string(),
                    'latitude' => Types::string(),
                    'longitude' => Types::string(),
                ];
            },
        ];
    }
}
