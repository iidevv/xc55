<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

/**
 * Class VendorType
 * @package XcartGraphqlApi\Types\Model
 */
class VendorType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'vendor',
            'description' => 'Vendor information model',
            'fields'      => function () {
                return [
                    'id' => Types::id(),
                    'email' => Types::string(),
                    'company_name' => Types::string(),
                    'image' => Types::string(),
                    'location' => Types::string(),
                    'description' => Types::string(),
                    'description_html' => Types::string(),
                ];
            },
        ];
    }
}
