<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * USPS
 */
class USPS extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\AShipping
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define shipping processor
     *
     * @return string
     */
    public static function defineProcessor()
    {
        return 'CDev\USPS\Model\Shipping\Processor\USPS';
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'CDev\USPS';
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'USPS_username' => [
                static::MODULE_FIELD_NAME        => 'userid',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
        ];
    }

    // }}} </editor-fold>

    /**
     * Import 'category' value
     *
     * @param \XLite\Model\Config $model  Config
     * @param string              $value  Value
     * @param array               $column Column info
     *
     * @return void
     */
    protected function importCategoryColumn(\XLite\Model\Config $model, $value, array $column)
    {
        static $processed = null;

        parent::importCategoryColumn($model, $value, $column);

        if ($processed === null) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'name'     => 'dataProvider',
                'category' => 'CDev\USPS',
                'value'    => 'USPS',
            ], true);

            $processed = true;
        }
    }
}
