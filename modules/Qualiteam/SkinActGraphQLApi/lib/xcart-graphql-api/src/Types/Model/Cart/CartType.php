<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Cart;

use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class CartType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'cart',
            'description' => 'Cart model',
            'fields'      => function () {
                return [
                    'id'                      => Types::id(),
                    'token'                   => Types::string(),
                    'cart_url'                => Types::string(),
                    'checkout_url'            => Types::string(),
                    'webview_flow_url'        => Types::string(),
                    'user'                    => [
                        'type'    => Types::byName('user'),
                        'resolve' => $this->createResolveForType('cartUser'),
                    ],
                    'address_list'            => [
                        'type'              => Types::listOf(Types::byName('address')),
                        'resolve'           => $this->createResolveForType('cartAddresses'),
                        'deprecationReason' => "Use cart.shipping_address and cart.billing_address instead separately. See user.address_list for more info.",
                    ],
                    'shipping_address'        => [
                        'type'    => Types::byName('address'),
                        'resolve' => $this->createResolveForType('cartShippingAddress'),
                    ],
                    'billing_address'         => [
                        'type'    => Types::byName('address'),
                        'resolve' => $this->createResolveForType('cartBillingAddress'),
                    ],
                    'items'                   => [
                        'type'    => Types::listOf(Types::byName('orderItem')),
                        'resolve' => $this->createResolveForType('cartItems'),
                    ],
                    'payment'                 => [
                        'type'    => Types::byName('paymentMethod'),
                        'resolve' => $this->createResolveForType('cartPayment'),
                    ],
                    'shipping'                => [
                        'type'    => Types::byName('shippingMethod'),
                        'resolve' => $this->createResolveForType('cartShipping'),
                    ],
                    'coupons'                 => [
                        'type'    => Types::listOf(Types::byName('coupon')),
                        'resolve' => $this->createResolveForType('cartCoupons'),
                    ],
                    'shipping_methods'        => [
                        'type'        => Types::listOf(Types::byName('shippingMethod')),
                        'description' => 'Available shipping methods for cart',
                    ],
                    'payment_methods'         => [
                        'type'        => Types::listOf(Types::byName('paymentMethod')),
                        'description' => 'Available payment methods for cart',
                    ],
                    'notes'                   => Types::string(),
                    'markups_list'            => Types::listOf(Types::string()),
                    'total'                   => Types::float(),
                    'total_amount'            => Types::int(),
                    'checkout_ready'          => Types::boolean(),
                    'payment_selection_ready' => Types::boolean(),
                    'same_address'            => Types::boolean(),
                    'errors'                  => Types::listOf(Types::byName('cartErrorEnum')),
                ];
            },
        ];
    }
}
