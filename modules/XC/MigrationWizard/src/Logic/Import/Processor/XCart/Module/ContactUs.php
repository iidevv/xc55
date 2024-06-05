<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Contact Us module
 */
class ContactUs extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['CDev\ContactUs'];
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}config"
            . " WHERE name = 'contact_us_fields' LIMIT 1"
        );
    }

    // }}} </editor-fold>
}
