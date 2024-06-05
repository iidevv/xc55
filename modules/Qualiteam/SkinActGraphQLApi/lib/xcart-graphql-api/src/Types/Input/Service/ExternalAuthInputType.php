<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input\Service;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class ExternalAuthInputType extends InputObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'ExternalAuthInput',
            'description' => 'User external authentication input type',
            'fields'      => function () {
                return [
                    'provider'     => Types::nonNull(Types::string()),
                    'access_token' => Types::nonNull(Types::byName('AccessToken')),
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
