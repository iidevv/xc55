<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor;

use XLite\Core\Converter;
use XLite\Model\Order;
use XLite\Model\Payment\Method;

/**
 * Fake payment method. Used for X-Payments allowed payment methods list if connector is not configured
 */
class XPaymentsAllowed extends AXPayments
{

    /**
     * Check - payment processor is applicable for specified order or not
     *
     * @param Order          $order  Order
     * @param Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(Order $order, Method $method)
    {
        return false;
    }

    /**
     * Get payment method configuration page URL
     *
     * @param Method $method    Payment method
     * @param boolean                     $justAdded Flag if the method is just added via administration panel. Additional init configuration can be provided
     *
     * @return string
     */
    public function getConfigurationURL(Method $method, $justAdded = false)
    {
        return ($this->getModuleId())
            ? Converter::buildURL(
                'xpc',
                '',
                ['section' => 'welcome']
            )
            : parent::getConfigurationURL($method, $justAdded);
    }

    /**
     * This is not Saved Card payment method
     *
     * @return boolean
     */
    protected function isSavedCardsPaymentMethod()
    {
        return false;
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return '';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        return [];
    }
}
