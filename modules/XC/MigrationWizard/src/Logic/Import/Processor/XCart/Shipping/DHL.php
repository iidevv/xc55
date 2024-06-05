<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * DHL
 */
class DHL extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\AShipping
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define shipping processor
     *
     * @return string
     */
    public static function defineProcessor()
    {
        return 'XC\DHL\Model\Shipping\Processor\DHL';
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'XC\DHL';
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'DHL_password' => [
                static::MODULE_FIELD_NAME        => 'api_password',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'DHL_id'       => [
                static::MODULE_FIELD_NAME        => 'site_id',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'DHL_account'  => [
                static::MODULE_FIELD_NAME        => 'dhl_account',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'DHL_testmode' => [
                static::MODULE_FIELD_NAME        => 'test_mode',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
        ];
    }

    // }}} </editor-fold>
}
