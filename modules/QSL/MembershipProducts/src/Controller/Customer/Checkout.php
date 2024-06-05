<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated controller for checkout-related actions.
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    public function processSucceed($fullProcess = true)
    {
        parent::processSucceed($fullProcess);

        /**
         * Apply membership, all actions and DB is completed at this stage
         */
        $this->getCart()->processApplyMembership();
    }

    protected function restoreOrder()
    {
        parent::restoreOrder();

        // Return original user membership level if his order reverts back to the "cart" state
        $this->getCart()->processCancelApplyMembership();
    }
}
