<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Controller\Admin;

use XC\Stripe\Core\OAuth;
use XC\Stripe\Model\Payment\Stripe;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Converter;
use Xlite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;

/**
 * Payment method
 * @Extender\Mixin
 */
abstract class PaymentMethod extends \XLite\Controller\Admin\PaymentMethod
{
    /**
     * Check visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getPaymentMethod();
    }

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        if (!$this->getAction()) {
            $method = $this->getPaymentMethod();
            if (
                $method->getProcessor() instanceof Stripe
                && $method->getSetting('accessToken')
                && !$method->getProcessor()->retrieveAcount()
            ) {
                $prefix = $method->getProcessor()->isTestMode($method) ? 'Test' : '';
                $method->setSetting('accessToken' . $prefix, null);
                $method->setSetting('publishKey' . $prefix, null);
                Database::getEM()->flush();

                TopMessage::addWarning(
                    'Your Stripe account is no longer accessible. Please connect with Stripe once again.'
                );
            }
            if (
                $method->getProcessor() instanceof Stripe
                && $method->isSettingsConfigured()
                && !Config::getInstance()->Security->customer_security
            ) {
                TopMessage::addWarning(
                    'The "Stripe" feature requires https to be properly set up for your store.',
                    [
                        'url' => Converter::buildURL('https_settings'),
                    ]
                );
            }
        }
        parent::run();
    }

    /**
     * Update payment method
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $method = $this->getPaymentMethod();
        if ($method->getProcessor() instanceof Stripe) {
            $oldTestValue = $method->getSetting('mode');
            Request::getInstance()->settings = array_merge(
                Request::getInstance()->settings,
                [
                    'payment_methods' => json_encode(
                        array_values(
                            array_filter(
                                Request::getInstance()->settings['payment_methods'] ?? [],
                                static fn(string $method): bool => Stripe::isPaymentMethodExist($method)
                            )
                        )
                    )
                ]
            );
        }

        parent::doActionUpdate();

        if ($method->getProcessor() instanceof Stripe) {
            if ($method->isSettingsConfigured() && !Config::getInstance()->Security->customer_security) {
                TopMessage::addWarning(
                    'The "Stripe" feature requires https to be properly set up for your store.',
                    [
                        'url' => Converter::buildURL('https_settings'),
                    ]
                );
            }

            if (empty($method->getEnabledPaymentMethods())) {
                TopMessage::addWarning('At least one payment method should be turned ON.');
            }

            $newTestValue = $method->getSetting('mode');
            $prefix = $method->getProcessor()->isTestMode($method) ? 'Test' : '';
            if ($newTestValue !== $oldTestValue && !$method->getSetting('accessToken' . $prefix)) {
                [, $error] = OAuth::getInstance()->refreshToken($method);

                if (!empty($error)) {
                    TopMessage::addError($error);
                    $method->setSetting('mode', $oldTestValue);
                    $this->setReturnURL(
                        Converter::buildURL(
                            'payment_method',
                            null,
                            ['method_id' => $method->getMethodId()]
                        )
                    );
                }

                Database::getEM()->flush();
            }
        }
    }
}
