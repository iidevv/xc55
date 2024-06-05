<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\DTO;

class CartDTO
{
    // CartErrors constants
    const EMPTY_CART                = 3000;
    const NO_PAYMENT_SELECTED       = 3001;
    const NO_SHIPPING_SELECTED      = 3002;
    const NO_SHIPPING_AVAILABLE     = 3003;
    const NO_SHIPPING_ADDRESS       = 3004;
    const NON_FULL_SHIPPING_ADDRESS = 3005;

    public $id;
    public $token;
    public $cart_url         = '';
    public $checkout_url     = '';
    public $webview_flow_url = '';
    public $user;
    public $address_list     = [];
    public $items            = [];
    public $payment;
    public $shipping;
    public $total;
    public $total_amount;
    public $checkout_ready;
    public $payment_selection_ready;
    public $same_address;
    public $errors           = [];
    public $coupons          = [];
    public $markups_list     = [];

    public $notes;

    public $cartModel;
}
