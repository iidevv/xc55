<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class AddressInputType extends InputObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'address_input',
            'description' => 'Address input type',
            'fields'      => function () {
                return [
                    'email' => Types::string(),
                    'title' => Types::string(),
                    'first_name' => Types::string(),
                    'last_name' => Types::string(),
                    'country_code' => Types::string(),
                    'state_code' => Types::string(),
                    'state_name' => Types::string(),
                    'county' => Types::string(),
                    'city' => Types::string(),
                    'address' => Types::string(),
                    'address2' => Types::string(),
                    'zip' => Types::string(),
                    'phone' => Types::string(),
                    'fax' => Types::string(),
                ];
            },
        ];
    }

    public function __construct()
    {
        $config = $this->configure();

        parent::__construct($config);
    }
}
