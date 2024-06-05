<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\AppData;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class CurrencyType
 */
class CurrencyType extends ObjectType
{
    public function configure()
    {
        return [
            'name'        => 'currency',
            'description' => 'Currency model',
            'fields'      => function () {
                return [
                    'code'                => Types::string(),
                    'name'                => Types::string(),
                    'prefix'              => Types::string(),
                    'suffix'              => Types::string(),
                    'e'                   => Types::int(),
                    'delimiter'           => Types::string(),
                    'thousands_delimiter' => Types::string(),
                ];
            },
        ];
    }
}
