<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Cart;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class PaymentMethodFieldType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'payment_field',
            'description' => 'Payment field model',
            'fields'      => function () {
                return [
                    'id'    => Types::id(),
                    'name'  => Types::string(),
                    'value' => Types::string(),
                ];
            },
        ];
    }
}
