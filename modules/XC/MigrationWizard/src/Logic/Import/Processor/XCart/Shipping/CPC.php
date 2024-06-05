<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * Canada Post
 */
class CPC extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\AShipping
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define shipping processor
     *
     * @return string
     */
    public static function defineProcessor()
    {
        return 'XC\CanadaPost\Model\Shipping\Processor\CanadaPost';
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'XC\CanadaPost';
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'CPC_username'       => [
                static::MODULE_FIELD_NAME        => 'user',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'CPC_password'       => [
                static::MODULE_FIELD_NAME        => 'password',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'CPC_wizard_enabled' => [
                static::MODULE_FIELD_NAME        => 'wizard_enabled',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'CPC_wizard_hash'    => [
                static::MODULE_FIELD_NAME        => 'wizard_hash',
                static::MODULE_FIELD_IS_REQUIRED => true,
            ],
            'CPC_quote_type'     => [
                static::MODULE_FIELD_NAME       => 'quote_type',
                static::MODULE_FIELD_IS_VIRTUAL => true,
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Initializers <editor-fold desc="Initializers" defaultstate="collapsed">

    public static function initFieldQuoteTypeValue()
    {
        return 'N';
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldWizardEnabledValue($value)
    {
        return $value === 'Y';
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">
    /**
     * Ignore Some Fields That Have Worthless Values. Are Useful For Shippings
     *
     * @param string $field_name
     *
     * @return string
     */
    protected static function addWorthlessFieldCondition($field_name)
    {
        $valueCondition = parent::addWorthlessFieldCondition($field_name);

        if (strpos($field_name, 'wizard_enabled') !== false) {
            $valueCondition .= " AND value <> 'Y'";
        }

        return $valueCondition;
    }
    // }}} </editor-fold>
}
