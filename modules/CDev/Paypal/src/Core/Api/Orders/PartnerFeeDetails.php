<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-partner_fee_details
 *
 * @property \CDev\Paypal\Core\Api\Orders\Payee    receiver
 * @property \CDev\Paypal\Core\Api\Orders\Currency amount
 */
class PartnerFeeDetails extends PayPalModel
{
    /**
     * @return \CDev\Paypal\Core\Api\Orders\Payee
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Payee $receiver
     *
     * @return PartnerFeeDetails
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * @return \CDev\Paypal\Core\Api\Orders\Currency
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\Currency $amount
     *
     * @return PartnerFeeDetails
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
