<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class UserType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'user',
            'description' => 'User model',
            'fields'      => function () {
                return [
                    'id'                  => Types::id(),
                    'user_type'           => Types::byName('userTypeEnum'),
                    'title'               => Types::string(),
                    'first_name'          => Types::string(),
                    'last_name'           => Types::string(),
                    'email'               => Types::string(),
                    'phone'               => Types::string(),
                    'enabled'             => Types::boolean(),
                    'registered'          => Types::boolean(),
                    'registered_date'     => Types::int(),
                    'last_login_date'     => Types::int(),
                    'language'            => Types::string(),
                    'address_list'      => [
                        'type' => Types::listOf(Types::byName('address')),
                        'resolve' => $this->createResolveForType('userAddressList')
                    ],
                    'orders_count'        => Types::int(),
                    // TODO Maybe union suitable for vendor_info?
                    'vendor_info'         => Types::listOf(Types::string()),
                    'auth_id'             => Types::string(),
                    'auth_token'          => Types::string(),
                    'login'               => Types::string(),
                    'company'             => Types::string(),
                    'url'                 => Types::string(),
                    'tax_number'          => Types::string(),
                    'contact_us_url'      => Types::string(),
                    'account_details_url' => Types::string(),
                    'orders_list_url'     => Types::string(),
                    'address_book_url'    => Types::string(),
                    'messages_url'        => Types::string(),
                ];
            },
        ];
    }
}