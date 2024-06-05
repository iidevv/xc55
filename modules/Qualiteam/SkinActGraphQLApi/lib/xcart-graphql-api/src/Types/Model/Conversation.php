<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * @package XcartGraphqlApi\Types\Model
 */
class Conversation extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'conversation',
            'description' => 'Vendors/Sellers conversation',
            'fields'      => function () {
                return [
                    'id'            => Types::id(),
                    'order_id'      => Types::string(),
                    'order_number'  => Types::string(),
                    'unreadCount'   => Types::int(),
                    'messages'      => Types::listOf(Types::byName('message')),
                    'messages_admin' => Types::listOf(Types::byName('message_admin')),
                    'messages_user' => Types::listOf(Types::byName('message_user')),
                ];
            },
        ];
    }
}
