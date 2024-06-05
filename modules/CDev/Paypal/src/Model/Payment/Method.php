<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model\Payment;

use XCart\Extender\Mapping\Extender;
use CDev\Paypal;

/**
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Payment\Method
{
    /**
     * Get payment processor class
     *
     * @return string
     */
    public function getClass()
    {
        $class = parent::getClass();
        $className = strpos($class, 'Module\\') === 0 ? substr($class, 7, strlen($class)) : $class;
        /** @var \XLite\Model\Payment\Base\Processor $processor */
        $processor = class_exists($className) ? $className::getInstance() : null;

        if (
            $this->getServiceName() == Paypal\Main::PP_METHOD_EC
            && $this->isForceMerchantAPI($processor)
        ) {
            $class = 'CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPI';
        }

        if (
            $this->getServiceName() == Paypal\Main::PP_METHOD_PC
            && $this->getExpressCheckoutPaymentMethod()
            && $this->getExpressCheckoutPaymentMethod()->isForceMerchantAPI($processor)
        ) {
            $class = 'CDev\Paypal\Model\Payment\Processor\PaypalCreditMerchantAPI';
        }

        return $class;
    }

    /**
     * Get payment method setting by its name
     *
     * @param string $name Setting name
     *
     * @return string
     */
    public function getSetting($name)
    {
        if ($this->getServiceName() === Paypal\Main::PP_METHOD_EC && $this->isForcedEnabled()) {
            $parentMethod = $this->getProcessor()->getParentMethod();
            $result = $parentMethod->getSetting($name);
        } elseif ($this->getServiceName() === Paypal\Main::PP_METHOD_PC) {
            $parentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

            $result = $this->isForwardingAllowedForSetting($name) && $parentMethod && $parentMethod->getSetting($name)
                ? $parentMethod->getSetting($name)
                : parent::getSetting($name);
        } else {
            $result = parent::getSetting($name);
        }

        return $result;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function isForwardingAllowedForSetting($name)
    {
        $parentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

        return $name !== 'email' || $parentMethod->getSetting('api_type') === 'email';
    }

    /**
     * Additional check for PPS
     *
     * @return boolean
     */
    public function isEnabled()
    {
        $result = parent::isEnabled();

        if ($result && $this->getServiceName() == Paypal\Main::PP_METHOD_PPS) {
            $result = !$this->getProcessor()->isPaypalAdvancedEnabled();
        }

        if ($result && $this->getServiceName() == Paypal\Main::PP_METHOD_PC) {
            $result = Paypal\Main::isExpressCheckoutEnabled() && $this->getSetting('enabled');
        }

        return $result;
    }

    /**
     * Set 'added' property
     *
     * @param boolean $added Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setAdded($added)
    {
        $result = parent::setAdded($added);

        if ($this->getServiceName() == Paypal\Main::PP_METHOD_EC) {
            if (!$added) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    [
                        'category' => 'CDev\Paypal',
                        'name'     => 'show_admin_welcome',
                        'value'    => 'N',
                    ]
                );
            }
        }

        return $result;
    }

    /**
     * Get Express Checkout payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getExpressCheckoutPaymentMethod()
    {
        return Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);
    }

    /**
     * Is forced Merchant API for Paypal Express
     * https://developer.paypal.com/docs/classic/api/#merchant
     *
     * @param \XLite\Model\Payment\Base\Processor $processor Payment processor
     *
     * @return boolean
     */
    protected function isForceMerchantAPI($processor)
    {
        $parentMethod = $processor
            ? $processor->getParentMethod()
            : null;

        return $processor
            && !$processor->isForcedEnabled($this)
            && (
                parent::getSetting('api_type') === 'email'
                || parent::getSetting('api_solution') === 'paypal'
                || ($parentMethod && !$processor->isConfigured($parentMethod))
            );
    }

    /**
     * Get warning note
     *
     * @return string
     */
    public function getWarningNote()
    {
        $message = parent::getWarningNote();

        if (
            $this->getProcessor()
            && in_array($this->getServiceName(), [Paypal\Main::PP_METHOD_PAD, Paypal\Main::PP_METHOD_PCP, Paypal\Main::PP_METHOD_PPA, Paypal\Main::PP_METHOD_EC], true)
            && $this->getProcessor()->getWarningNote($this)
        ) {
            $message = $this->getProcessor()->getWarningNote($this);
        }

        return $message;
    }

    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        $message = parent::getNotSwitchableReason();

        if (
            $this->getProcessor()
            && $this->getServiceName() === Paypal\Main::PP_METHOD_PAD
            && $this->getProcessor()->getWarningNote($this)
        ) {
            $message = static::t(
                'To enable this payment method, you need <Multi-vendor> module installed.',
                [
                    'link'  => \XLite::getInstance()->getServiceURL(
                        '#/available-addons',
                        null,
                        [
                            'tag' => 'Catalog Management',
                            'search' => 'Multi-vendor'
                        ]
                    )
                ]
            );
        }

        if (
            $this->getProcessor()
            && $this->getServiceName() === Paypal\Main::PP_METHOD_PFM
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'multi-vendor':
                    $message = static::t(
                        'To enable this payment method, you need <Multi-vendor> module installed.',
                        [
                            'link'  => \XLite::getInstance()->getServiceURL(
                                '#/available-addons',
                                null,
                                [
                                    'tag' => 'Catalog Management',
                                    'search' => 'Multi-vendor'
                                ]
                            )
                        ]
                    );
                    break;

                case 'https':
                    $message = static::t(
                        'Payments with this payment method are not allowed because HTTPS is not configured',
                        [
                            'url' => \XLite\Core\Converter::buildURL('https_settings')
                        ]
                    );
                    break;
                case 'conflict':
                    $message = static::t(
                        'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                    );
                    break;
            }
        }

        if (
            $this->getProcessor()
            && $this->getServiceName() === Paypal\Main::PP_METHOD_PCP
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'https':
                    $message = static::t(
                        'Payments with this payment method are not allowed because HTTPS is not configured',
                        [
                            'url' => \XLite\Core\Converter::buildURL('https_settings')
                        ]
                    );
                    break;
                case 'conflict':
                    $message = static::t(
                        'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                    );
                    break;
            }
        }

        if (
            $this->getProcessor()
            && $this->getServiceName() === Paypal\Main::PP_METHOD_EC
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'conflict':
                    $message = static::t(
                        'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                    );
                    break;
            }
        }

        if (
            $this->getProcessor()
            && $this->getServiceName() === Paypal\Main::PP_METHOD_PPA
        ) {
            switch ($this->getProcessor()->getNotSwitchableReasonType($this)) {
                case 'conflict':
                    $message = static::t(
                        'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                    );
                    break;
            }
        }

        return $message;
    }
}
