<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class RegistrationResultType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'registrationResult',
            'description' => 'Registration result',
            'fields'      => function () {
                return [
                    'id' => Types::id(),
                    'jwt' => Types::string(),
                    'user' => Types::byName('user'),
                ];
            },
        ];
    }
}
