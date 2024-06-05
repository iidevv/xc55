<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * FedEx
 */
class Fedex extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\AShipping
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define shipping processor
     *
     * @return string
     */
    public static function defineProcessor()
    {
        return 'CDev\FedEx\Model\Shipping\Processor\FEDEX';
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'CDev\FedEx';
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'FEDEX_account_number' => [
                static::MODULE_FIELD_NAME        => 'account_number',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'FEDEX_meter_number'   => [
                static::MODULE_FIELD_NAME        => 'meter_number',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'FEDEX_password'       => [
                static::MODULE_FIELD_NAME        => 'password',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'FEDEX_key'            => [
                static::MODULE_FIELD_NAME        => 'key',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'FEDEX_test_server'    => [
                static::MODULE_FIELD_NAME        => 'test_mode',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
        ];
    }

    // }}} </editor-fold>
}
