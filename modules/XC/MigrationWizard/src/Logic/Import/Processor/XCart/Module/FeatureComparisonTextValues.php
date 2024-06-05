<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XLite\InjectLoggerTrait;
use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration,

    XC\MigrationWizard\Logic\Import\Processor\XCart\Module\FeatureComparison;

/**
 * Feature Comparison module
 */
class FeatureComparisonTextValues extends \XLite\Logic\Import\Processor\AttributeValues\AttributeValueText
{
    use InjectLoggerTrait;

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $group = FeatureComparison::FC_GROUP;
        $type = \XLite\Model\Attribute::TYPE_TEXT;

        $fvalue = ", IFNULL(fvl.variant_name, pfo.value) AS `value`";
        if (!static::isTableExists('feature_variants')) {
            $fvalue = ', pfo.value AS `value`';
        }

        return "p.productcode AS `productSKU`"
            . ", fo.option_name AS `name`"
            . ", fc.class AS `class`"
            . ", '{$group}' AS `group`"
            . ", '{$type}' AS `type`"
            . ", '' AS `owner`"
            . $fvalue
            . ", '' AS `default`"
            . ", '' AS `priceModifier`"
            . ", '' AS `weightModifier`"
            . ", '' AS `editable`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $tp = self::getTablePrefix();
        $id_generator_place_holder = self::GENERATOR_PLACEHOLDER;

        $variantValue = '';
        $featureVariantsLanguageJoinSQL = '';
        if (static::isTableExists('feature_variants')) {
                $variantValue = " LEFT JOIN {$tp}feature_variants AS fv"
                . " ON fv.`foptionid` = fo.`foptionid`";

            $featureVariantsLanguageJoinSQL = static::callVersionSpecificFunction(
                'getFeatureVariantsLanguageJoinSQL',
                'XC\MigrationWizard\Logic\Import\Processor\XCart\Module\FeatureComparison'
            );
        }

        return "{$tp}product_features AS pf"
            . " INNER JOIN {$tp}feature_classes AS fc"
                . " ON fc.`fclassid` = pf.`fclassid`"
                . "{$id_generator_place_holder}"
            . " INNER JOIN {$tp}feature_options AS fo"
                . " ON fo.`fclassid` = fc.`fclassid`"
                . " AND fo.`option_type` IN ('D','N','T')"
            . $variantValue
            . " INNER JOIN {$tp}product_foptions AS pfo"
                . " ON pfo.`foptionid` = fo.`foptionid`"
                . " AND pfo.`productid` = pf.`productid`"
            . "{$featureVariantsLanguageJoinSQL}"
            . " INNER JOIN {$tp}products AS p"
                . " ON p.`productid` = pf.`productid`";
    }

    /**
     * Define ID generator data
     *
     * @return array
     */
    public static function defineIdGenerator()
    {
        $tp = self::getTablePrefix();

        return [
            'table' => "{$tp}product_features",
            'alias' => 'pf',
            'order' => ['pf.productid', 'pf.fclassid'],
        ];
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $result = '1';

        if (static::isDemoModeMigration()) {
            $productIds = static::getDemoProductIds();
            if (!empty($productIds)) {
                $productIds = implode(',', $productIds);
                $result = "p.productid IN ({$productIds})";
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $dataset = self::defineDataset();

        return Configuration::isModuleEnabled(Configuration::MODULE_FEATURE_COMPARISON)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$dataset} LIMIT 1"
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
        return static::t('Migrating product classes');
    }

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    protected function createModel(array $data)
    {
        $product = $this->getProduct($data['productSKU']);

        $result = null;
        if ($product) {
            $result = parent::createModel($data);
        } else {
            $this->getLogger('migration_errors')->debug('', ['processor' => get_called_class(), 'error' => 'Product not found', 'data' => $data]);
        }

        return $result;
    }
}
