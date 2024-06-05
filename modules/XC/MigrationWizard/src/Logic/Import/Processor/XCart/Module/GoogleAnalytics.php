<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * GoOgleAnalytics module
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 */
class GoogleAnalytics extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">
    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'CDev\GoogleAnalytics';
    }


    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'ganalytics_code'     => [
                static::MODULE_FIELD_NAME => 'ga_account',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'ganalytics_e_commerce_analysis'     => [
                static::MODULE_FIELD_NAME => 'ecommerce_enabled',
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        $gaCode = static::getCellData(
            "SELECT value FROM {$prefix}config WHERE category = 'Google_Analytics' AND name = 'ganalytics_code'"
        );
        return $gaCode != '';
    }
    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldEcommerceEnabledValue($value)
    {
        return $value === 'Y';
    }

    // }}} </editor-fold>
}
