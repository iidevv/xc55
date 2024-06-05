<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input\User;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

/**
 * Class UserDataInput
 * @package XcartGraphqlApi\Types\Input\User
 */
class UserUpdateInput extends InputObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'UserUpdateInput',
            'description' => 'Update user input type',
            'fields'      => function () {
                return [
                    'password'      => Types::string(),
                    'password_conf' => Types::string(),
                    'login'         => Types::string(),
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
