<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Enum;

use GraphQL\Type\Definition\EnumType;
use XcartGraphqlApi\DTO\CartDTO;

class CartErrorEnumType extends EnumType
{
    /**
     * EnumType constructor.
     */
    public function __construct()
    {
        $config = [
            'name'        => 'cart_error',
            'description' => 'Cart error enumeration',
            'values'      => [
                'EMPTY_CART'            => [
                    'value'       => CartDTO::EMPTY_CART,
                    'description' => 'Cart is empty'
                ],
                'NO_PAYMENT_SELECTED'   => [
                    'value'       => CartDTO::NO_PAYMENT_SELECTED,
                    'description' => 'No payment selected'
                ],
                'NO_SHIPPING_SELECTED'  => [
                    'value'       => CartDTO::NO_SHIPPING_SELECTED,
                    'description' => 'No shipping selected'
                ],
                'NO_SHIPPING_AVAILABLE' => [
                    'value'       => CartDTO::NO_SHIPPING_AVAILABLE,
                    'description' => 'No shipping available'
                ],
                'NO_SHIPPING_ADDRESS'   => [
                    'value'       => CartDTO::NO_SHIPPING_ADDRESS,
                    'description' => 'No shipping address'
                ],
                'NON_FULL_SHIPPING_ADDRESS'   => [
                    'value'       => CartDTO::NON_FULL_SHIPPING_ADDRESS,
                    'description' => 'Non full shipping address, some required fields are invalid'
                ],
            ]
        ];

        parent::__construct($config);
    }
}
