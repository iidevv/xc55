<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;
use XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal;

/**
 * Abstract PayPal sub processor
 */
abstract class APayPal extends PayPal
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const SOLUTION_ADVANCED    = 'advanced';
    public const SOLUTION_EXPRESS     = 'express';
    public const SOLUTION_PAYFLOWLINK = 'payflowlink';
    public const SOLUTION_REDIRECT    = 'redirect';
    public const SOLUTION_STANDARD    = 'ipn';

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define sub processors
     *
     * @return array
     */
    public static function defineSubProcessors()
    {
        return [];
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'use_preauth' => [
                static::MODULE_FIELD_NAME => 'transaction_type',
            ],
            'testmode'    => [
                static::MODULE_FIELD_NAME => 'mode',
            ],
            'param06'     => [
                static::MODULE_FIELD_NAME => 'prefix',
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    public static function getApiSolution()
    {
        return Configuration::getConfigurationOptionValue('paypal_solution');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldTransactionTypeValue($value)
    {
        return $value === 'Y' ? 'A' : 'S';
    }

    public function normalizeFieldModeValue($value)
    {
        return $value === 'Y' ? 'test' : 'live';
    }

    // }}} </editor-fold>
}
