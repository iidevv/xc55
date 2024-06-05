<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Manufacturers module
 */
class Manufacturers extends \XLite\Logic\Import\Processor\Attributes
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $type = \XLite\Model\Attribute::TYPE_SELECT;

        return '"Manufacturer" `name`,'
            . 'NULL `product`,'
            . 'NULL `class`,'
            . '"Manufacturers" `group`,'
            . 'NULL `options`,'
            . "'{$type}' `type`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        return \XC\MigrationWizard\Logic\Import\Recordset::NO_DATA_SOURCE;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        return Configuration::isModuleEnabled(Configuration::MODULE_MANUFACTURERS)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$prefix}manufacturers m"
                . ' WHERE m.avail = "Y" LIMIT 1'
            );
    }

    // }}} </editor-fold>

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating manufacturers');
    }

    /**
     * Change Shop_by_brand_field_id Right After Model Saving
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        static $is_done = false;

        if (
            $res = parent::importData($data)
            && !$is_done
            && $this->currentlyProcessingModel
            && $this->currentlyProcessingModel instanceof \XLite\Model\Attribute
        ) {
            $is_done = true;
            // There Was Optimization And General Flush Removed. We Need It To Work Search In Rep In Logic/Import/Processor/XCart/Module/ManufacturersValues Properly
            static::databaseGetEmFlush();
            if (
                ($rules = static::getMigrationWizard()->getStep('DetectTransferableData')->getSelectedRules())
                && in_array('XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ManufacturersValuesAdvancedFields', $rules, true)
            ) {
                if (!$this->currentlyProcessingModel->getId()) {
                    // For The Just Created Model. Flush To Obtain Id
                    static::databaseGetEmFlush();
                }
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    [
                        'category' => 'QSL\ShopByBrand',
                        'name'     => 'shop_by_brand_field_id',
                        'value'    => $this->currentlyProcessingModel->getId(),
                    ]
                );
            }
        }

        return $res;
    }
}
