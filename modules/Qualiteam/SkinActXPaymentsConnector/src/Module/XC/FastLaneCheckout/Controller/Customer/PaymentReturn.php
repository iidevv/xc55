<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Module\XC\FastLaneCheckout\Controller\Customer;

use XC\FastLaneCheckout\Main;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Session;

/**
 * Return to the store
 *
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsConnector","XC\FastLaneCheckout"})
 */
class PaymentReturn extends \XLite\Controller\Customer\PaymentReturn
{
    /**
     * Return
     *
     * @return void
     */
    protected function doActionReturn()
    {
        parent::doActionReturn();

        if (Main::isFastlaneEnabled()) {

            $transaction = $this->detectTransaction();
            if (
                $transaction
                && $transaction->isXpc()
                && $transaction->getOrder()->hasUnpaidTotal()
            ) {
                // Set flag only if payment has been canceled and cart is not converted to order
                Session::getInstance()->returnedAfterXpc = true;
            }

        }

    }
}
