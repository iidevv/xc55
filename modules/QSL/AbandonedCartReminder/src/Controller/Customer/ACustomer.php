<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Decorated method that runs the controller.
     *
     * @return void
     */
    protected function run()
    {
        parent::run();

        if (!$this->isServiceController()) {
            $cart = $this->getCart();
            // If a recovered cart became abandoned again, drop the "recovered" flag
            $cart->renewCartRecoveredFlag();
            // Update the date the cart was viewed by the customer the last time
            $cart->renewLastVisitDate();

            \XLite\Core\Database::getEM()->flush();
        }
    }
}
