<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\LifetimeHook;

use XC\Stripe\Model\Payment\Stripe;
use XLite\Core\Database;
use XLite\Model\Payment\Method;
use XLite\Model\Payment\MethodSetting;

final class Hook
{
    public function onUpgradeTo5505(): void
    {
        /** @var Method $paymentMethod */
        $paymentMethod = Database::getRepo('XLite\Model\Payment\Method')->findOneBy([
            'moduleName'   => 'XC_Stripe',
            'service_name' => 'Stripe',
        ]);

        if ($paymentMethod) {
            /** @var MethodSetting[] $paymentMethodSettings */
            $paymentMethodSettings = Database::getRepo('XLite\Model\Payment\MethodSetting')->findBy([
                'payment_method' => $paymentMethod,
            ]);
            /** @var MethodSetting|null $paymentMethods */
            $paymentMethods = Database::getRepo('XLite\Model\Payment\MethodSetting')->findOneBy([
                'payment_method' => $paymentMethod,
                'name'           => 'payment_methods',
            ]);
            $entityManager = Database::getEM();
            if (!$paymentMethods) {
                $paymentMethods = new MethodSetting();
                $paymentMethods->setPaymentMethod($paymentMethod);
                $paymentMethods->setName('payment_methods');
                $enabledPaymentMethods = Stripe::getDefaultPaymentMethodsEnabled();

                if ($paymentMethodSettings) {
                    $convertToBool = static fn($value): bool => in_array(
                        $value,
                        ['1', 'true', 'on', 'yes', true, 1],
                        true
                    );
                    foreach ($paymentMethodSettings as $setting) {
                        switch ($setting->getName()) {
                            case 'credit_card_method':
                                if (!$convertToBool($setting->getValue())) {
                                    $enabledPaymentMethods = array_values(
                                        array_filter(
                                            $enabledPaymentMethods,
                                            static fn(string $method): bool => $method !== 'card'
                                        )
                                    );
                                }
                                break;
                            case 'alipay_method':
                                if ($convertToBool($setting->getValue())) {
                                    $enabledPaymentMethods[] = 'alipay';
                                }
                                break;
                            case 'fpx_method':
                                if ($convertToBool($setting->getValue())) {
                                    $enabledPaymentMethods[] = 'fpx';
                                }
                                break;
                            case 'grab_pay_method':
                                if ($convertToBool($setting->getValue())) {
                                    $enabledPaymentMethods[] = 'grabpay';
                                }
                                break;
                        }
                    }
                }
                $paymentMethods->setValue(json_encode($enabledPaymentMethods));
                $entityManager->persist($paymentMethods);
            }
            array_walk(
                $paymentMethodSettings,
                static function (MethodSetting $setting) use ($entityManager): void {
                    if (
                        in_array(
                            $setting->getName(),
                            ['credit_card_method', 'alipay_method', 'fpx_method', 'grab_pay_method'],
                            true
                        )
                    ) {
                        $entityManager->remove($setting);
                    }
                }
            );

            $entityManager->flush();
        }
    }
}
