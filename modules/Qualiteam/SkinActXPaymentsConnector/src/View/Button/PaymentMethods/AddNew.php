<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Button\PaymentMethods;

use XLite\Core\Config;
use XLite\View\Button\Link;

/**
 * Add new payment method
 */
class AddNew extends Link
{
    /**
     * Link to the payment methods page 
     *
     * @return string
     */
    protected function getLocationURL() 
    {
        return Config::getInstance()->Qualiteam->SkinActXPaymentsConnector
            ->xpc_xpayments_url . 'admin.php?target=payment_confs';
    }

}

