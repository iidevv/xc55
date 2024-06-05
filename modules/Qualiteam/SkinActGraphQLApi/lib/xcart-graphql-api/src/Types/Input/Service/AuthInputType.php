<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input\Service;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class AuthInputType extends InputObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'AuthInput',
            'description' => 'User authentication input type',
            'fields'      => function () {
                return [
                    'login'     => Types::string(),
                    'password'  => Types::string(),
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
