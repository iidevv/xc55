<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping;

/**
 * Abstract shipping
 */
abstract class AShipping extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule implements \XC\MigrationWizard\Logic\Import\Processor\XCart\Shipping\IShipping
{
    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    /**
     * Setting creation rule
     *
     * @var string
     */
    protected $createRule = self::CONFIG_SETTING_MUST_EXIST;

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    public static function getProcessor()
    {
        static $processors = [];

        $class = get_called_class();

        if (!isset($processors[$class])) {
            $processor = static::defineProcessor();
            $processors[$class] = new $processor();
        }

        return $processors[$class]->getProcessorId();
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldTestModeValue($value)
    {
        return $value === 'Y';
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

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

        if ($processed === null) {
            $methods = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->findBy(
                [
                    'processor' => static::getProcessor(),
                    'added' => false,
                    'enabled' => false,
                ]
            );

            if ($methods) {
                foreach ($methods as $method) {
                    $method->setAdded(true);
                    $method->setEnabled(true);
                    \XLite\Core\Database::getEM()->persist($method);
                }
            }

            $processed = true;
        }
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

        $worthless_values = ['', 'N'];// Used To Check Data Presence
        // Ignore Empty And N Values In Check Data Presence For RealTime Shipping To Avoid Module Installations

        if (
            strpos($field_name, 'testmode') !== false
            || strpos($field_name, 'test_mode') !== false
        ) {
            $worthless_values[] = 'Y';
        }
        $valueCondition .= " AND value NOT IN ('" . implode("','", $worthless_values) . "')";

        return $valueCondition;
    }
    // }}} </editor-fold>
}
