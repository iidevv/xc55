<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Volume discount module
 */
class VolumeDiscounts extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['CDev\VolumeDiscounts'];
    }

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        $isDiscountsExist = (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}orders WHERE discount > 0 LIMIT 1"
        );

        return $isDiscountsExist;
    }

    // }}} </editor-fold>
}
