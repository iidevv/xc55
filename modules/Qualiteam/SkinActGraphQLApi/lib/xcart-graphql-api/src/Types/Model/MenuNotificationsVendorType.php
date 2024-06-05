<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class MenuNotificationsVendorType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'menuNotificationsVendor',
            'description' => 'Notifications in dropdown menu for vendor',
            'fields'      => function () {
                return [
                    'messages'      => Types::int(),
                    'orders'        => Types::int(),
                    'offers'        => Types::int(),
                    'questions'     => Types::int(),
                ];
            },
        ];
    }
}
