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
class MessageUserType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'message_user',
            'description' => 'Message user model',
            'fields'      => function () {
                return [
                    'id'            => Types::id(),
                    'title'         => Types::string(),
                    'date_time'     => Types::string(),
                    'author'        => Types::string(),
                    'read'          => Types::boolean(),
                ];
            },
        ];
    }
}
