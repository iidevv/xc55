<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment;

/**
 * EWay payment
 */
class EWay extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\APayment
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
                static::MODULE_FIELD_NAME        => 'api_key',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param02'     => [
                static::MODULE_FIELD_NAME        => 'password',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param05'     => [
                static::MODULE_FIELD_NAME => 'verify_phone',
            ],
            'param06'     => [
                static::MODULE_FIELD_NAME => 'verify_email',
            ],
            'testmode'    => [
                static::MODULE_FIELD_NAME        => 'mode',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param03'     => [
                static::MODULE_FIELD_NAME => 'prefix',
            ],
            'use_preauth' => [
                static::MODULE_FIELD_NAME => 'type',
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
        return 'XC\EWAYStoredShared';
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * @return string
     */
    public static function getProcessor()
    {
        return 'cc_eway_hosted.php';
    }

    /**
     * @return string
     */
    public static function getMethodClass()
    {
        return 'XC\EWAYStoredShared\Model\Payment\Processor\EWAYStoredShared';
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldModeValue($value)
    {
        return $value === 'Y' ? 'test' : 'live';
    }

    public function normalizeFieldTypeValue($value)
    {
        return $value === 'Y' ? 'auth' : 'sale';
    }

    // }}} </editor-fold>
}
