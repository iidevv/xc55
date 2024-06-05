<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal;

/**
 * PayPal Advanced processor
 */
class PaypalAdvanced extends APayPal
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

        if ($solution !== static::SOLUTION_ADVANCED) {
            return [];
        }

        $fields = [
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
        ];

        return array_merge(parent::defineConfigset(), $fields);
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * @return string
     */
    public static function getProcessor()
    {
        return 'ps_paypal_advanced.php';
    }

    /**
     * @return string
     */
    public static function getMethodClass()
    {
        return 'CDev\Paypal\Model\Payment\Processor\PaypalAdvanced';
    }

    // }}} </editor-fold>
}
