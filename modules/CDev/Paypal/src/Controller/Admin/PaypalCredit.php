<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Admin;

use CDev\Paypal;

/**
 * Paypal Credit settings controller
 */
class PaypalCredit extends \CDev\Paypal\Controller\Admin\PaypalSettings
{
    /**
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && in_array(\XLite\Core\Config::getInstance()->Company->location_country, Paypal\Main::PP_PAYPAL_CREDIT_COUNTRIES, true);
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PC);
        }

        return $this->paymentMethod && $this->paymentMethod->getModuleName() === static::MODULE_NAME
            ? $this->paymentMethod
            : null;
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'CDev\Paypal\View\Model\PaypalCredit';
    }
}
