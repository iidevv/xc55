<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\DTO;

class UserDTO
{
    const USER_TYPE_CUSTOMER    = 'customer';
    const USER_TYPE_ANONYMOUS   = 'anonymous';
    const USER_TYPE_STAFF       = 'admin';
    const USER_TYPE_VENDOR      = 'vendor';

    public $id;
    public $user_type;
    public $title;
    public $first_name;
    public $last_name;
    public $email;
    public $login;
    public $enabled;
    public $phone;
    public $registered;
    public $registered_date;
    public $last_login_date;
    public $orders_count;
    public $vendor_info;
    public $language;
    public $address_list = [];

    public $auth_id;
    public $auth_token;

    public $contact_us_url;
    public $account_details_url;
    public $orders_list_url;
    public $address_book_url;
    public $messages_url;

    public $profileModel;
}
