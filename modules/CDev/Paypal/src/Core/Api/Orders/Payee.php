<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-payee
 *
 * @property string                                                         email
 * @property string                                                         merchant_id
 * @property \CDev\Paypal\Core\Api\Orders\PayeeDisplayMetadata payee_display_metadata
 */
class Payee extends PayPalModel
{
    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return Payee
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    /**
     * @param string $merchant_id
     *
     * @return Payee
     */
    public function setMerchantId($merchant_id)
    {
        $this->merchant_id = $merchant_id;

        return $this;
    }

    /**
     * @return \CDev\Paypal\Core\Api\Orders\PayeeDisplayMetadata
     */
    public function getPayeeDisplayMetadata()
    {
        return $this->payee_display_metadata;
    }

    /**
     * @param \CDev\Paypal\Core\Api\Orders\PayeeDisplayMetadata $payee_display_metadata
     *
     * @return Payee
     */
    public function setPayeeDisplayMetadata($payee_display_metadata)
    {
        $this->payee_display_metadata = $payee_display_metadata;

        return $this;
    }
}
