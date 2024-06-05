<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal;

/**
 * PayPal WPS processor
 */
class PaypalWPS extends APayPal
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

        if ($solution !== static::SOLUTION_STANDARD) {
            return [];
        }

        $fields = array_merge(parent::defineConfigset(), [
            'param08' => [
                static::MODULE_FIELD_NAME        => 'account',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'param09' => [
                static::MODULE_FIELD_NAME        => 'description',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
        ]);

        unset($fields['use_preauth']);

        return $fields;
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * @return string
     */
    public static function getProcessor()
    {
        return 'ps_paypal.php';
    }

    /**
     * @return string
     */
    public static function getMethodClass()
    {
        return 'CDev\Paypal\Model\Payment\Processor\PaypalWPS';
    }

    // }}} </editor-fold>
}
