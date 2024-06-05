<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment;

/**
 * 2Checkout payment
 */
class TwoCheckout extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\APayment
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'param01'  => [
                static::MODULE_FIELD_NAME        => 'account',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param03'  => [
                static::MODULE_FIELD_NAME        => 'secret',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param05'  => [
                static::MODULE_FIELD_NAME => 'language',
            ],
            'testmode' => [
                static::MODULE_FIELD_NAME        => 'mode',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param02'  => [
                static::MODULE_FIELD_NAME => 'prefix',
            ],
            'currency' => [
                static::MODULE_FIELD_NAME => 'currency',
            ],
        ];
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'CDev\TwoCheckout';
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * @return string
     */
    public static function getProcessor()
    {
        return 'cc_2conew.php';
    }

    /**
     * @return string
     */
    public static function getMethodClass()
    {
        return 'CDev\TwoCheckout\Model\Payment\Processor\TwoCheckout';
    }

    // }}} </editor-fold>

    // {{{ Initializers <editor-fold desc="Initializers" defaultstate="collapsed">

    public static function initFieldCurrencyValue()
    {
        return 'USD';
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldModeValue($value)
    {
        return $value === 'Y' ? 'test' : 'live';
    }

    public function normalizeFieldLanguageValue($value)
    {
        return strtolower($value);
    }

    // }}} </editor-fold>
}
