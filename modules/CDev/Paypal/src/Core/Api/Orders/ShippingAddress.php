<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Api\Orders;

use PayPal\Api\Address;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-shipping_address
 *
 * @property string recipient_name
 */
class ShippingAddress extends Address
{
    /**
     * @param string $recipient_name
     *
     * @return ShippingAddress
     */
    public function setRecipientName($recipient_name)
    {
        $this->recipient_name = $recipient_name;

        return $this;
    }
}
