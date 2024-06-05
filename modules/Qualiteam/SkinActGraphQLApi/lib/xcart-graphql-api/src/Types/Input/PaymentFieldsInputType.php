<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class PaymentFieldsInputType extends InputObjectType
{
    public function __construct()
    {
        $config = [
            'name'   => 'payment_fields_input',
            'fields' => [
                'id'    => [
                    'type'        => Types::id(),
                    'description' => 'Payment fields id'
                ],
                'value' => [
                    'type'        => Types::string(),
                    'description' => 'Payment fields value'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
