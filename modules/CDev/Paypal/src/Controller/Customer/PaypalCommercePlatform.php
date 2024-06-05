<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Controller\Customer;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use CDev\Paypal;
use CDev\Paypal\Model\Payment\Processor\PaypalCommercePlatform as PaypalCommercePlatformProcessor;
use XLite\Model\Payment\MethodSetting;

class PaypalCommercePlatform extends \XLite\Controller\Customer\Checkout
{
    /**
     * https://developer.paypal.com/docs/checkout/reference/server-integration/set-up-transaction/#on-the-server
     */
    public function doActionCreateOrder()
    {
        $paymentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);

        $this->getCart()->setPaymentMethod($paymentMethod);

        $transaction = $this->getCart()->getFirstOpenPaymentTransaction();

        if ($transaction) {
            /** @var PaypalCommercePlatformProcessor $processor */
            $processor = $paymentMethod->getProcessor();
            $result    = $processor->createOrder($transaction);

            \XLite\Core\Database::getEM()->flush();

            if (
                \XLite\Core\Request::getInstance()->hostedFields
                && (!$result || (is_array($result) && isset($result['message'])))
            ) {
                TopMessage::addError($result['message'] ?? 'Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.');

                $this->setReturnURL($this->buildURL('checkout'));
                $this->setHardRedirect();
                return;
            }

            $this->printAJAX($result);
            $this->silent = true;
            $this->setSuppressOutput(true);
        }
    }

    public function doActionOnApprove()
    {
        $paymentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);
        $processor     = $paymentMethod->getProcessor();

        $requestData = \XLite\Core\Request::getInstance()->data;

        $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
            ->findOneByCell('PaypalOrderId', $requestData['orderID']);

        if ($transaction) {
            $processor->onApprove($transaction, \XLite\Core\Request::getInstance()->data);

            $orderDetails = $processor->getPaypalOrder($transaction->getDataCell('PaypalOrderId')->getValue());
            if ($orderDetails) {
                $this->requestData = [
                    'email'          => $orderDetails->payer->email_address ?? null, // See condition in \XLite\Controller\Customer\Checkout::updateAnonymousProfile()
                    'create_profile' => false,
                ];

                $purchaseUnit = $orderDetails->purchase_units[0] ?? [];
                if (isset($purchaseUnit->shipping)) {
                    $address = $purchaseUnit->shipping->address;

                    $street = ($address->address_line_1 ?? '')
                        . (isset($address->address_line_2) && $address->address_line_2 !== 'n/a'
                            ? (' ' . $address->address_line_2)
                            : '');

                    $this->requestData['shippingAddress'] = [
                        'name'         => $purchaseUnit->shipping->name->full_name ?? '',
                        'street'       => $street,
                        'country_code' => $address->country_code ?? '',
                        'state'        => $address->admin_area_1 ?? '',
                        'city'         => $address->admin_area_2 ?? '',
                        'zipcode'      => $address->postal_code ?? '',
                    ];

                    $this->requestData['billingAddress'] = $this->requestData['shippingAddress'];
                    $this->requestData['same_address']   = true;
                }

                $profile = $this->getProfile();
                if (!$profile && $this->getCart()) {
                    $profile = $this->getCart()->getProfile();
                }

                if (!\XLite\Core\Auth::getInstance()->isLogged() && (!$profile || !$profile->getLogin())) {
                    $this->updateProfile();
                }

                $modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
                if ($modifier && $modifier->canApply()) {
                    $this->updateShippingAddress();
                }

                $this->updateBillingAddress();

                $this->setCheckoutAvailable();

                $this->updateCart();
            }

            \XLite\Core\Database::getEM()->flush();

            $this->setHardRedirect();
            $this->setReturnURL($this->buildURL('checkout'));
        } else {
            \XLite\Core\TopMessage::addWarning('Transaction not fond');
        }
    }

    /**
     * https://developer.paypal.com/docs/checkout/reference/server-integration/authorize-transaction/#on-the-server
     */
    public function doActionAuthorizeOrder()
    {
    }

    /**
     * https://developer.paypal.com/docs/checkout/reference/server-integration/capture-transaction/#on-the-server
     */
    public function doActionCaptureOrder()
    {
    }

    protected function getCreateOrderData()
    {
    }

    public function doActionCheckThreeDSecureResponse()
    {
        $paymentMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);
        $orderId = Request::getInstance()->order_id;

        $transaction = Database::getRepo('XLite\Model\Payment\Transaction')->findOneByCell('PaypalOrderId', $orderId);
        $result = [];

        if ($transaction) {
            $processor = $paymentMethod->getProcessor();
            $response = $processor->get3dsResponse($orderId);

            $liabilityShift = $response->liability_shift ?? null;
            $enrollmentStatus = $response->three_d_secure->enrollment_status ?? null;
            $authenticationStatus = $response->three_d_secure->authentication_status ?? null;

            /** @var MethodSetting|null $setting3ds */
            $setting3ds = Database::getRepo('XLite\Model\Payment\MethodSetting')->findOneBy([
                'name'           => '3d_secure',
                'payment_method' => $paymentMethod
            ]);
            $is3dsEnabled = $setting3ds && $setting3ds->getValue();

            if (
                ($enrollmentStatus === 'Y' && $authenticationStatus === 'Y' && $liabilityShift === 'POSSIBLE')
                || ($enrollmentStatus === 'Y' && $authenticationStatus === 'A' && $liabilityShift === 'POSSIBLE')
                || ($enrollmentStatus === 'N' && !$authenticationStatus && $liabilityShift === 'NO')
                || ($enrollmentStatus === 'U' && !$authenticationStatus && $liabilityShift === 'NO')
                || ($enrollmentStatus === 'B' && !$authenticationStatus && $liabilityShift === 'NO')
                || (!$enrollmentStatus && !$authenticationStatus && !$liabilityShift && !$is3dsEnabled)
            ) {
                $result['passed'] = true;
            } elseif (
                ($enrollmentStatus === 'Y' && $authenticationStatus === 'U' && $liabilityShift === 'UNKNOWN')
                || ($enrollmentStatus === 'Y' && $authenticationStatus === 'U' && $liabilityShift === 'NO')
                || ($enrollmentStatus === 'Y' && $authenticationStatus === 'C' && $liabilityShift === 'UNKNOWN')
                || ($enrollmentStatus === 'Y' && !$authenticationStatus && $liabilityShift === 'NO')
                || ($enrollmentStatus === 'U' && !$authenticationStatus && $liabilityShift === 'UNKNOWN')
                || (!$enrollmentStatus && !$authenticationStatus && $liabilityShift === 'UNKNOWN')
            ) {
                $result = [
                    'passed'  => false,
                    'message' => [
                        'type'    => 'warning',
                        'message' => static::t('Unable to complete authentication. Please try again.')
                    ]
                ];
            } elseif (
                ($enrollmentStatus === 'Y' && $authenticationStatus === 'N' && $liabilityShift === 'NO')
                || ($enrollmentStatus === 'Y' && $authenticationStatus === 'R' && $liabilityShift === 'NO')
            ) {
                $result = [
                    'passed'  => false,
                    'message' => [
                        'type'    => 'warning',
                        'message' => static::t('Please, use another card or payment method.')
                    ]
                ];
            } else {
                $result = [
                    'passed'  => false,
                    'message' => [
                        'type'    => 'warning',
                        'message' => static::t('Unknown error. Please try again.')
                    ]
                ];
            }
        }

        $this->printAJAX($result);
        $this->silent = true;
        $this->setSuppressOutput(true);
    }
}
