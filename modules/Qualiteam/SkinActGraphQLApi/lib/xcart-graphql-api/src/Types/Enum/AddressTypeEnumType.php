<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Enum;

use GraphQL\Type\Definition\EnumType;

class AddressTypeEnumType extends EnumType
{
    const SHIPPING_TYPE = 'SHIPPING_TYPE';
    const BILLING_TYPE = 'BILLING_TYPE';

    /**
     * EnumType constructor.
     */
    public function __construct()
    {
        $config = [
            'name'        => 'address_type_enum',
            'description' => 'Address type enumeration',
            'values'      => [
                'SHIPPING_TYPE'            => [
                    'value'       => static::SHIPPING_TYPE,
                    'description' => 'Shipping address'
                ],
                'BILLING_TYPE'   => [
                    'value'       => static::BILLING_TYPE,
                    'description' => 'Billing address'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
