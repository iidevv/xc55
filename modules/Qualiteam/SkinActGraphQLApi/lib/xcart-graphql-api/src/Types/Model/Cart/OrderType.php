<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\Types\Model\Cart;

use phpDocumentor\Reflection\Type;
use XcartGraphqlApi\Types;
use XcartGraphqlApi\Types\ObjectType;

class OrderType extends ObjectType
{
    /**
     * @return mixed
     */
    protected function configure()
    {
        return [
            'name'        => 'order',
            'description' => 'Order model',
            'fields'      => function () {
                return [
                    'orderId'                 => Types::id(),
                    'orderNumber'             => Types::string(),
                    'total'                   => Types::float(),
                    'subtotal'                => Types::float(),
                    'shippingCost'            => Types::float(),
                    'paymentFee'              => Types::float(),
                    'taxAmount'               => Types::float(),
                    'discountValue'           => Types::float(),
                    'currency'                => Types::string(),
                    'items'                   => [
                        'type'    => Types::listOf(Types::byName('orderItem')),
                        'resolve' => $this->createResolveForType('cartItems'),
                    ],
                    'orderDate'               => Types::string(),
                    'orderTime'               => Types::string(),
                    'marketplaceId'           => Types::string(),
                    'updateDate'              => Types::string(),
                    'trackingNumber'          => Types::string(),
                    'customerNotes'           => Types::string(),
                    'adminNotes'              => Types::string(),
                    'user'                    => [
                        'type'    => Types::byName('user'),
                        'resolve' => $this->createResolveForType('cartUser'),
                    ],
                    'paymentMethod'  => Types::string(),
                    'paymentStatus'  => Types::string(),
                    'paymentStatusStr'  => Types::string(),
                    'shippingMethod' => Types::string(),
                    'shippingStatusStr' => Types::string(),
                    'shippingStatusBar' => Types::listOf(Types::byName('shippingStatusBar')),
                    'shippingStatus' => Types::string(),
                    'shipping_address'        => [
                        'type'    => Types::byName('address'),
                        'resolve' => $this->createResolveForType('cartShippingAddress'),
                    ],
                    'billing_address'         => [
                        'type'    => Types::byName('address'),
                        'resolve' => $this->createResolveForType('cartBillingAddress'),
                    ],
                    'transactions'      => [
                        'type'    => Types::listOf(Types::byName('transaction')),
                        'resolve' => $this->createResolveForType('orderTransactions'),
                    ],
                    'adminOrderUri' => Types::string(),
                    'customerOrderUri' => Types::string(),
                    'unreadMessages' => Types::int(),
                    'deliveredDate' => Types::string(),
                    'trackingUrls' => Types::listOf(Types::string())
                ];
            },
        ];
    }
}
