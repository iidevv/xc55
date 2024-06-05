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
 * Class VendorPlanType
 * @package XcartGraphqlApi\Types\Model
 */
class VendorPlanType extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'vendorPlan',
            'description' => 'Vendor plan information model',
            'fields'      => function () {
                return [
                    'id' => Types::id(),
                    'name' => Types::string(),
                    'price' => Types::float(),

                    'level1' => Types::float(),
                    'level2' => Types::float(),
                    'level3' => Types::float(),
                    'level4' => Types::float(),
                    'level5' => Types::float(),

                    'new_products_limit'    => Types::int(),
                    'extra_charge'          => Types::float(),
                    'is_selected'           => Types::boolean(),
                    'is_signup'             => Types::boolean(),
                ];
            },
        ];
    }
}
