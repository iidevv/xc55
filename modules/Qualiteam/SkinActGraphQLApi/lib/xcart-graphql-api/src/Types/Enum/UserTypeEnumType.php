<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Enum;

use GraphQL\Type\Definition\EnumType;
use XcartGraphqlApi\DTO\UserDTO;

class UserTypeEnumType extends EnumType
{
    /**
     * EnumType constructor.
     */
    public function __construct()
    {
        $config = [
            'name'        => 'user_type',
            'description' => 'User type enumeration',
            'values'      => [
                'USER_TYPE_CUSTOMER'            => [
                    'value'       => UserDTO::USER_TYPE_CUSTOMER,
                    'description' => 'Customer'
                ],
                'USER_TYPE_ANONYMOUS'   => [
                    'value'       => UserDTO::USER_TYPE_ANONYMOUS,
                    'description' => 'Anonymous customer'
                ],
                'USER_TYPE_STAFF'  => [
                    'value'       => UserDTO::USER_TYPE_STAFF,
                    'description' => 'Staff'
                ],
                'USER_TYPE_VENDOR'  => [
                    'value'       => UserDTO::USER_TYPE_VENDOR,
                    'description' => 'Vendor'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
