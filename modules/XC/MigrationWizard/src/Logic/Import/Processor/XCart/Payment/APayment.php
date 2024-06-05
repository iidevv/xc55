<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment;

/**
 * Abstract payment
 */
abstract class APayment extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    /**
     * Setting creation rule
     *
     * @var string
     */
    protected $createRule = self::CONFIG_SETTING_MUST_EXIST;

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
            'payment_method' => [
                static::COLUMN_IS_KEY => true,
            ],
            'name'           => [
                static::COLUMN_IS_KEY => true,
            ],
            'value'          => [],
        ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $class = addslashes(static::getMethodClass());

        return "'{$class}' `payment_method`,"
            . 'name `name`,'
            . 'value `value`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp        = self::getTablePrefix();
        $processor = static::getProcessor();
        $config    = static::getConfigset();

        $fields = array_keys($config);

        $sources = [];

        foreach ($fields as $index => $field) {
            $fieldInfo = $config[$field];

            $fieldValue     = static::defineFieldValue($fieldInfo, $field);
            $valueCondition = static::defineFieldValueCondition($fieldInfo);

            $sources[] = "SELECT PM{$index}.`paymentid` AS `paymentid`"
                . ", CC{$index}.`processor` AS `processor`"
                . ", '{$fieldInfo[static::MODULE_FIELD_NAME]}' as `name`"
                . ", {$fieldValue} as `value`"
                . " FROM {$tp}payment_methods AS PM{$index}"
                . " INNER JOIN {$tp}ccprocessors AS CC{$index}"
                . " ON CC{$index}.`paymentid` = PM{$index}.`paymentid`"
                . " AND PM{$index}.`active` = 'Y'"
                . " AND CC{$index}.`processor` LIKE '{$processor}'"
                . " HAVING `value` IS NOT NULL{$valueCondition}";
        }

        $source = implode(' UNION ', $sources);

        return $source ? "($source) AS PD" : '';
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Return processor
     *
     * @return string
     */
    public static function getProcessor()
    {
        return __CLASS__;
    }

    /**
     * Return method class
     *
     * @return string
     */
    public static function getMethodClass()
    {
        return __CLASS__;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * Add field condition
     *
     * @param array $field
     *
     * @return string
     */
    protected static function addFieldCondition($field, $withOR = false, $fieldNameAsCondition = true)
    {
        return parent::addFieldCondition($field, $withOR, $fieldNameAsCondition);
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'payment method' value
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function normalizePaymentMethodValue($value)
    {
        $method = null;

        if (!$this->verifyValueAsEmpty($value)) {
            $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['class' => $value]);
        }

        return $method;
    }

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeNameValue($value)
    {
        return $value;
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Import 'Payment method' value
     *
     * @param \XLite\Model\Payment\MethodSetting $model  Method setting
     * @param string                             $value  Value
     * @param array                              $column Column info
     *
     * @return void
     */
    protected function importPaymentMethodColumn(\XLite\Model\Payment\MethodSetting $model, $value, array $column)
    {
        $paymentMethod          = $this->normalizePaymentMethodValue($value);
        $paymentMethod->enabled = true;
        $paymentMethod->added   = true;

        $model->setPaymentMethod($paymentMethod);
    }

    // }}} </editor-fold>
}
