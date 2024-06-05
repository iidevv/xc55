<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types;

use XcartGraphqlApi\Types;

/**
 * Class AppDataType
 * @package XcartGraphqlApi\Types
 */
class AuthLinksType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'authLinks',
            'description' => 'Auth Links',
            'fields'      => function () {
                return [
                    'registration'     => Types::string(),
                    'password_change'  => Types::string(),
                ];
            },
        ];
    }
}
