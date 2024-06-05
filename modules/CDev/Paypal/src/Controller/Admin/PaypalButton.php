<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Admin;

class PaypalButton extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Paypal module string name for payment methods
     */
    public const MODULE_NAME = 'CDev_Paypal';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $paymentMethod = $this->getPaymentMethod();

        return $paymentMethod
            ? $paymentMethod->getName()
            : '';
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'CDev\Paypal\View\Model\PaypalButton';
    }

    public function doActionUpdate()
    {
        $list = new \CDev\Paypal\View\ItemsList\Model\PaypalButton();
        $list->processQuick();

        $this->getModelForm()->performAction('update');
    }

    /**
     * Get method id from request
     *
     * @return integer
     */
    public function getMethodId()
    {
        return \XLite\Core\Request::getInstance()->method_id;
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = $this->getMethodId()
                ? \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($this->getMethodId())
                : null;
        }

        return $this->paymentMethod && $this->paymentMethod->getModuleName() === static::MODULE_NAME
            ? $this->paymentMethod
            : null;
    }

    /**
     * Get SignUp url
     *
     * @return string
     */
    public function getSignUpUrl()
    {
        return $this->getPaymentMethod()->getReferralPageURL($this->getPaymentMethod());
    }

    /**
     * Is In-Context Boarding SignUp available
     *
     * @return boolean
     */
    public function isInContextSignUpAvailable()
    {
        $api = \CDev\Paypal\Main::getRESTAPIInstance();

        return $api->isInContextSignUpAvailable();
    }
}
