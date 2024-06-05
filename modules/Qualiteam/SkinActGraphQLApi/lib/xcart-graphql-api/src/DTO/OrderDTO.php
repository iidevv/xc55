<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\DTO;

class OrderDTO
{
    public $orderId;
    public $orderNumber;
    public $total;
    public $subtotal;
    public $shippingCost;
    public $paymentFee;
    public $taxAmount;
    public $discountValue;
    public $currency;
    public $orderDate;
    public $orderTime;
    public $marketplaceId;
    public $updateDate;
    public $trackingNumber;
    public $customerNotes;
    public $adminNotes;
    public $user;
    public $paymentMethod;
    public $paymentStatus;
    public $paymentStatusStr;
    public $shippingMethod;
    public $shippingStatus;
    public $shippingStatusStr;
    public $shippingStatusBar;
    public $shipping_address;
    public $billing_address;

    public $items = [];

    public $cartModel;
    public $adminOrderUri;
    public $customerOrderUri;
    public $unreadMessages;
    public $deliveredDate;
    public $trackingUrls;
}
