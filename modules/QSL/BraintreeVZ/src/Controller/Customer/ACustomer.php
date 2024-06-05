<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BraintreeVZ\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract controller for Customer interface
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Assemble updateCart event
     *
     * @return boolean
     */
    protected function assembleEvent()
    {
        $result = parent::assembleEvent();

        if ($result) {
            \XLite\Core\Event::braintreeTotalUpdate(
                [
                    'total' => $this->getCart()->getTotal(),
                    'currency' => $this->getCart()->getCurrency()->getCode(),
                ]
            );
        }

        return $result;
    }

}
