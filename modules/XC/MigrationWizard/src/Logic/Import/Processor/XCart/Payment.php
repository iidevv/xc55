<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

/**
 * Payment methods processor
 */
class Payment extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define sub processors
     *
     * @return array
     */
    public static function defineSubProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\AuthorizeNet',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\Moneybookers',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\TwoCheckout',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\EWay',
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
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\OfflinePayments',
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
            'payment_method' => [
                static::COLUMN_IS_KEY => true,
            ],
            'name' => [
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
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\MethodSetting');
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
        return static::t('Payments migrated');
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating payment settings');
    }

    // }}} </editor-fold>
}
