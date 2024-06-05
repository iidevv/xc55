<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Cart;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class PaymentType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'payment',
            'description' => 'Payment model',
            'fields'      => function () {
                return [
                    'id'    => Types::id(),
                    'service_name'  => Types::string(),
                    'payment_name'  => Types::string(),
                    'details'       => Types::string(),
                    'fields'        => Types::listOf(Types::byName('paymentMethodField')),
                ];
            },
        ];
    }
}
