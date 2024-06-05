<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Payment\BackendTransaction;
use CDev\Paypal;
use CDev\Paypal\Model\Payment\Processor\PaypalCommercePlatform as PaypalCommercePlatformProcessor;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        $list = parent::defineCommonJSData();

        $method = null;
        if (Paypal\Main::isExpressCheckoutEnabled()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);
        } elseif (Paypal\Main::isPaypalForMarketplacesEnabled()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PFM);
        } elseif (Paypal\Main::isPaypalCommercePlatformEnabled()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);
        }

        if ($method && $processor = $method->getProcessor()) {
            $list['PayPalEnvironment'] = $processor->isTestMode($method) ? 'sandbox' : 'production';

            if (Paypal\Main::isPaypalCommercePlatformEnabled()) {
                $paypalSDKParams = [
                    'client-id' => $method->getSetting('client_id'),
                ];

                //if ($processor->isTestMode($method)) {
                //    $paypalSDKParams['debug'] = 'true';
                //}
                //$paypalSDKParams['buyer-country'] = 'US';

                $paypalSDKParams['currency'] = \XLite::getInstance()->getCurrency()->getCode();

                if ($processor->getInitialTransactionType($method) === BackendTransaction::TRAN_TYPE_SALE) {
                    $paypalSDKParams['intent'] = 'capture';
                } else {
                    $paypalSDKParams['intent'] = 'authorize';
                }

                $enableFunding = [];

                $components = ['buttons', 'funding-eligibility'];
                if ($this instanceof \XLite\Controller\Customer\Checkout) {
                    $paypalSDKParams['commit'] = 'true';

                    $components[] = 'hosted-fields';

                    $clientToken = $processor->generateClientToken();

                    $list['PayPalClientToken'] = $clientToken;

                    $disabledFundingMethods = $method->getSetting('disabledFundingMethods');
                    $disableFunding         = $disabledFundingMethods
                        ? json_decode($disabledFundingMethods)
                        : [];

                    // In case there was an error during the JSON encoding or there is a non-array value in the database.
                    if (!is_array($disableFunding)) {
                        $disableFunding = [];
                    }

                    $venmoEnabled = $method->getSetting('venmoEnabled');
                    if ($venmoEnabled) {
                        $enableFunding[] = 'venmo';
                    } else {
                        $disableFunding[] = 'venmo';
                    }
                } else {
                    $paypalSDKParams['commit'] = 'false';

                    $disableFunding = ['card'];
                }

                if (Paypal\Main::isPaypalCreditForCommercePlatformEnabled()) {
                    $creditMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PC);
                    if ($creditMethod->getSetting('ppcm_enabled')) {
                        $components[] = 'messages';
                    }
                    if (!in_array('credit', $disableFunding)) {
                        // #XCB-840 force enable paylater to work properly for AU, FR, GB, DE
                        $enableFunding = array_merge($enableFunding, ['credit', 'paylater']);
                    }
                } else {
                    $disableFunding[] = 'credit';
                }

                if ($enableFunding) {
                    $paypalSDKParams['enable-funding'] = implode(',', array_unique($enableFunding));
                }

                if ($disableFunding) {
                    $paypalSDKParams['disable-funding'] = implode(',', array_unique($disableFunding));
                }

                if ($components) {
                    $paypalSDKParams['components'] = implode(',', $components);
                }

                $list['PayPalSDKParams']            = http_build_query($paypalSDKParams);
                $list['PayPal3Dsecure']             = $method->getSetting('3d_secure');
                $list['PayPalPartnerAttributionId'] = PaypalCommercePlatformProcessor::BN_CODE;
            }
        }

        return $list;
    }
}
