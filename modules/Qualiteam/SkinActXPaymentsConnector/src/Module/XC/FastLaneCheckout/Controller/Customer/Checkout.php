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
 * Checkout 
 *
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsConnector","XC\FastLaneCheckout"})
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Check if customer is returned from X-Payments page
     *
     * @return boolean
     */
    public function isReturnedAfterXpc()
    {
        $result = Session::getInstance()->returnedAfterXpc;

        Session::getInstance()->returnedAfterXpc = null;

        return $result;
    }

    /**
     * Clear init data from session and redirect back to checkout
     *
     * @return void
     */
    protected function doActionClearInitData()
    {
        if (Main::isFastlaneEnabled()) {
            Session::getInstance()->returnedAfterXpc = true;
        }

        parent::doActionClearInitData();
    }
}
