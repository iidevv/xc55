<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal;

/**
 * Payflow Transparent Redirect processor
 */
class PayflowTransparentRedirect extends APayPal
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

        if ($solution !== static::SOLUTION_REDIRECT) {
            return [];
        }

        $fields = [
            'param01'     => [
                static::MODULE_FIELD_NAME        => 'api_username',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param02'     => [
                static::MODULE_FIELD_NAME        => 'api_password',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param05'     => [
                static::MODULE_FIELD_NAME        => 'signature',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param06'     => [
                static::MODULE_FIELD_NAME => 'prefix',
            ],
            'use_preauth' => [
                static::MODULE_FIELD_NAME        => 'auth_method',
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
        return 'ps_paypal_redirect.php';
    }

    /**
     * @return string
     */
    public static function getMethodClass()
    {
        return 'CDev\Paypal\Model\Payment\Processor\PayflowTransparentRedirect';
    }

    // }}} </editor-fold>
}
