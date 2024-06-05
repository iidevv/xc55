<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class AddressType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'address',
            'description' => 'Address model',
            'fields'      => function () {
                return [
                    'id' => Types::id(),
                    'type' => [
                        'type' => Types::byName('addressTypeEnum'),
                        'deprecationReason' => 'Deprecated together with cart.address_list'
                    ],
                    'email' => Types::string(),
                    'title' => Types::string(),
                    'first_name' => Types::string(),
                    'last_name' => Types::string(),
                    'country' => [
                        'type' => Types::byName('country'),
                        'resolve' => $this->createResolveForType('addressCountry'),
                    ],
                    'state' => [
                        'type' => Types::byName('state'),
                        'resolve' => $this->createResolveForType('addressState'),
                    ],
                    'county' => Types::string(),
                    'city' => Types::string(),
                    'address' => Types::string(),
                    'address2' => Types::string(),
                    'zip' => Types::string(),
                    'phone' => Types::string(),
                    'fax' => [
                       'type' => Types::string(),
                       'deprecationReason' => 'Default X-Cart system doesn\'t have fax field'
                    ]
                ];
            },
        ];
    }
}
