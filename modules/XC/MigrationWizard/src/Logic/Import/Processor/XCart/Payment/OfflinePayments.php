<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Offline payment methods
 */
class OfflinePayments extends \XLite\Logic\Import\Processor\AProcessor
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const PAYMENT_SCRIPT = 'payment_offline.php';

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    protected static $associations = [
        2  => [
            'service_name' => 'PurchaseOrder',
            'class'        => 'XLite\Model\Payment\Processor\PurchaseOrder',
        ],
        4  => [
            'service_name' => 'PhoneOrdering',
            'class'        => 'XLite\Model\Payment\Processor\PhoneOrdering',
        ],
        7  => [
            'service_name' => 'MoneyOrdering',
            'class'        => 'XLite\Model\Payment\Processor\Offline',
        ],
        8  => [
            'service_name' => 'COD',
            'class'        => 'XLite\Model\Payment\Processor\Offline',
        ],
        13  => [
            'service_name' => 'FaxOrdering',
            'class'        => 'XLite\Model\Payment\Processor\Offline',
        ],
        16 => [
            'service_name' => 'Echeck',
            'class'        => 'XLite\Model\Payment\Processor\Check',
        ],
    ];

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'payment_id'   => [],
            'service_name' => [
                static::COLUMN_IS_KEY => true,
            ],
            'class'        => [
                static::COLUMN_IS_KEY => true,
            ],
            'name'         => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'description'  => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'added'        => [],
            'enabled'      => [],
            'xc4EntityId'  => [],
        ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $lng = Configuration::getDefaultCustomerLanguage();

        return 'pm.paymentid AS `payment_id`,'
            . 'pm.paymentid AS `xc4EntityId`,'
            . 'pm.payment_method AS `service_name`,'
            . 'pm.payment_template AS `class`,'
            . "pm.payment_method AS `name_{$lng}`,"
            . "pm.payment_details AS `description_{$lng}`,"
            . 'TRUE AS `added`,'
            . '"O" AS `type`,'
            . "IF(pm.`active` = 'Y', TRUE, FALSE) AS `enabled`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = static::getTablePrefix();

        return "{$tp}payment_methods AS pm";
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $payment_script = self::PAYMENT_SCRIPT;

        // Select Purchase Order / Phone Ordering Etc Anyway To Disactivate It In Case
        return "(pm.`payment_script` = '{$payment_script}' OR pm.`payment_template` = 'customer/main/payment_chk.tpl') AND (pm.`active`='Y')";
    }

    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [
            self::REGISTRY_SOURCE => 'payment_id',
            self::REGISTRY_RESULT => 'method_id',
        ];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'service name' value
     *
     * @param mixed $value Value
     *
     * @return string|null
     */
    protected function normalizeServiceNameValue($value)
    {
        return empty(static::$associations[$this->currentRowData['payment_id']])
            ? \XLite\Core\Converter::convertToCamelCase(str_replace([' ', "'", '"', '&', '/', '\\', '+', '-', '*'], '_', $value))
            : static::$associations[$this->currentRowData['payment_id']]['service_name'];
    }

    /**
     * Normalize 'class' value
     *
     * @param mixed $value Value
     *
     * @return string|null
     */
    protected function normalizeClassValue($value)
    {
        return empty(static::$associations[$this->currentRowData['payment_id']])
            ? 'XLite\Model\Payment\Processor\Offline'
            : static::$associations[$this->currentRowData['payment_id']]['class'];
    }

    // }}} </editor-fold>

    /**
     * Import 'zone name' value
     *
     * @param \XLite\Model\Zone $zone   Zone
     * @param string            $value  Value
     * @param array             $column Column info
     *
     * @return void
     */
    protected function importPaymentIdColumn(\XLite\Model\Payment\Method $zone, $value, array $column)
    {
    }
}
