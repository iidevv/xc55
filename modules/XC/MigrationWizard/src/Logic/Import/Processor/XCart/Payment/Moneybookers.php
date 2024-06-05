<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment;

/**
 * Moneybookers payment
 */
class Moneybookers extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\APayment
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'CDev\Moneybookers';
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * @return string
     */
    public static function getProcessor()
    {
        return 'cc_mbookers.php';
    }

    /**
     * @return string
     */
    public static function getMethodClass()
    {
        return 'CDev\Moneybookers\Model\Payment\Processor\Moneybookers';
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * Return TRUE if source data exists
     *
     * @return boolean
     */
    public static function hasTransferableData()
    {
        return false;
    }

    // }}} </editor-fold>
}
