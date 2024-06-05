<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment;

/**
 * Authorize.Net payment
 */
class AuthorizeNet extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\APayment
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
            'param01'     => [
                static::MODULE_FIELD_NAME        => 'login',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param02'     => [
                static::MODULE_FIELD_NAME        => 'key',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'use_preauth' => [
                static::MODULE_FIELD_NAME => 'type',
            ],
            'testmode'    => [
                static::MODULE_FIELD_NAME        => 'test',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param04'     => [
                static::MODULE_FIELD_NAME => 'prefix',
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
        return 'CDev\AuthorizeNet';
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    public static function getProcessor()
    {
        return 'cc_authorizenet_sim.php';
    }

    public static function getMethodClass()
    {
        return 'CDev\AuthorizeNet\Model\Payment\Processor\AuthorizeNetSIM';
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldTestValue($value)
    {
        return $value === 'Y' ? 1 : 0;
    }

    public function normalizeFieldTypeValue($value)
    {
        return $value === 'Y' ? 'auth' : 'sale';
    }

    // }}} </editor-fold>
}
