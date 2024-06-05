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
 * Class ProductAdditionalInfo
 * @package XcartGraphqlApi\Types\Model
 */
class ProductAdditionalInfo extends ObjectType
{
    /**
     * @return array|mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'productAdditionalInfo',
            'description' => 'Additional financial info for product page',
            'fields'      => function () {
                return [
                    'approximate_shipping_rate_range'   => Types::string(),
                    'selling_fee_percent'               => Types::string(),
                    'selling_fee_sum'                   => Types::string(),
                    'you_earn_min'                      => Types::string(),
                    'you_earn_max'                      => Types::string(),
                ];
            },
        ];
    }
}
