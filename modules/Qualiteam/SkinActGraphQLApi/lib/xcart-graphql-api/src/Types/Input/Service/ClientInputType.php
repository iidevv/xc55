<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Input\Service;

use GraphQL\Type\Definition\InputObjectType;
use XcartGraphqlApi\Types;

class ClientInputType extends InputObjectType
{
    protected function configure()
    {
        return [
            'name'        => 'ClientInput',
            'description' => 'Client input type',
            'fields'      => function () {
                return [
                    'unique_id'         => Types::nonNull(Types::string()),
                    'app_id'            => Types::nonNull(Types::string()),
                    'app_version'       => Types::string(),
                    'push_id'           => Types::string(),
                    'platform'          => Types::nonNull(Types::string()),
                    'system_name'       => Types::string(),
                    'system_version'    => Types::string(),
                    'device_name'       => Types::string(),
                    'manufacturer'      => Types::string(),
                    'model'             => Types::string(),
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
