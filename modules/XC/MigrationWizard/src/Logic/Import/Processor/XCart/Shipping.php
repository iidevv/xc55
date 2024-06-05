<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Shipping processor
 */
class Shipping extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define extra processors
     *
     * @return array
     */
    public static function definePreProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Zones',
        ];
    }

    /**
     * Define sub processors
     *
     * @return array
     */
    public static function defineSubProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\AP',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\CPC',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\DHL',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\Fedex',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\UPS',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\USPS',
        ];
    }

    /**
     * Define extra processors
     *
     * @return array
     */
    public static function definePostProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\Custom',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_IS_KEY => true,
            ],
            'category' => [
                static::COLUMN_IS_KEY => true,
            ],
            'value' => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config');
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Shippings migrated');
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating shipping settings');
    }

    // }}} </editor-fold>
}
