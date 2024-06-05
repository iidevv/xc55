<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * Australia Post
 */
class AP extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\AShipping
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define shipping processor
     *
     * @return string
     */
    public static function defineProcessor()
    {
        return 'CDev\AustraliaPost\Model\Shipping\Processor\AustraliaPost';
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'CDev\AustraliaPost';
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return  [
            'AP_apikey' =>  [
                static::MODULE_FIELD_NAME => 'api_key',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'AP_testmode' =>  [
                static::MODULE_FIELD_NAME => 'test_mode',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ]
        ];
    }

    // }}} </editor-fold>
}
