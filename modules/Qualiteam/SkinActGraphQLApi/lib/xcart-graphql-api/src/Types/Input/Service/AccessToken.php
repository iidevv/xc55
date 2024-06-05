<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input\Service;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class AccessToken extends InputObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'AccessToken',
            'description' => 'User external authentication access token',
            'fields'      => function () {
                return [
                    'access_token'     => Types::nonNull(Types::string()),
                    'resource_owner_id' => Types::id(),
                    'refresh_token'    => Types::string(),
                    'expires_in'       => Types::int(),
                    'expires'         => Types::int(),
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
