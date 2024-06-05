<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model\Payment;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Converter;
use XC\Stripe\Main;

/**
 * Payment method model
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Payment\Method
{
    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        $message   = parent::getNotSwitchableReason();
        $processor = $this->getProcessor();

        if (
            $processor
            && in_array($this->getServiceName(), [Main::STRIPE_SERVICE_NAME, Main::STRIPE_CONNECT_SERVICE_NAME], true)
            && $processor->isSettingsConfigured($this)
        ) {
            if (
                $this->getServiceName() === Main::STRIPE_CONNECT_SERVICE_NAME
                && !\Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC\MultiVendor')
            ) {
                return static::t(
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
                $this->getServiceName() === Main::STRIPE_CONNECT_SERVICE_NAME
                && Main::getStripeMethod()->getEnabled()
                || $this->getServiceName() === Main::STRIPE_SERVICE_NAME
                && Main::getStripeConnectMethod()->getEnabled()
            ) {
                return static::t('The "StripeConnect" cannot work with "Stripe" at the same time');
            }

            if (!Config::getInstance()->Security->customer_security) {
                return static::t(
                    'The "Stripe" feature requires https to be properly set up for your store.',
                    [
                        'url' => Converter::buildURL('https_settings'),
                    ]
                );
            }
        }

        return $message;
    }
}
