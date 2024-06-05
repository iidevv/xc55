<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * PayPal Express Checkout processor
 */
class ExpressCheckout extends APayPal
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        $solution = static::getApiSolution();

        if ($solution !== static::SOLUTION_EXPRESS) {
            return [];
        }

        $apiType = static::getExpressApiType();

        $commonFields = [
            'api_type'      => [
                static::MODULE_FIELD_NAME        => 'api_type',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'api_solution'  => [
                static::MODULE_FIELD_NAME        => 'api_solution',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'buyNowEnabled' => [
                static::MODULE_FIELD_NAME => 'buyNowEnabled',
            ],
        ];

        $APIs = [
            'email'   => [
                'paypal_express_email' => [
                    static::MODULE_FIELD_NAME        => 'email',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
            ],
            'api'     => [
                'param01' => [
                    static::MODULE_FIELD_NAME        => 'api_username',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
                'param02' => [
                    static::MODULE_FIELD_NAME        => 'api_password',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
                'param07' => [
                    static::MODULE_FIELD_NAME        => 'auth_method',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
                'param05' => [
                    static::MODULE_FIELD_NAME        => 'signature',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
            ],
            'payflow' => [
                'param02' => [
                    static::MODULE_FIELD_NAME        => 'partner',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
                'param01' => [
                    static::MODULE_FIELD_NAME        => 'vendor',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
                'param04' => [
                    static::MODULE_FIELD_NAME        => 'user',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
                'param05' => [
                    static::MODULE_FIELD_NAME        => 'pwd',
                    static::MODULE_FIELD_IS_REQUIRED => true,
                ],
            ],
        ];

        return array_merge(parent::defineConfigset(), $commonFields, $APIs[$apiType]);
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    public static function getProcessor()
    {
        return 'ps_paypal_pro.php';
    }

    public static function getMethodClass()
    {
        return 'CDev\Paypal\Model\Payment\Processor\ExpressCheckout';
    }

    public static function getExpressApiType()
    {
        return Configuration::getConfigurationOptionValue('paypal_express_method');
    }

    public static function getEmail()
    {
        return Configuration::getConfigurationOptionValue('paypal_express_email');
    }

    // }}} </editor-fold>

    // {{{ Initializers <editor-fold desc="Initializers" defaultstate="collapsed">

    public static function initFieldApiTypeValue()
    {
        return static::getExpressApiType() !== 'email' ? 'api' : 'email';
    }

    public static function initFieldApiSolutionValue()
    {
        return static::getExpressApiType() === 'api' ? 'paypal' : 'payflow';
    }

    public static function initFieldEmailValue()
    {
        return static::getEmail();
    }

    public static function initFieldBuyNowEnabledValue()
    {
        return true;
    }

    // }}} </editor-fold>
}
