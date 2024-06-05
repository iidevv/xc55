<?php
// phpcs:ignoreFile
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Feature Comparison module
 */
class FeatureComparison extends \XLite\Logic\Import\Processor\Attributes
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const FC_GROUP = '';

    public const OPTION_TYPE_BOOLEAN  = 'B';
    public const OPTION_TYPE_DATE     = 'D';  // not supported in XC5
    public const OPTION_TYPE_MULTIPLE = 'M';  // not supported in XC5
    public const OPTION_TYPE_NUMERIC  = 'N';  // not supported in XC5
    public const OPTION_TYPE_SINGLE   = 'S';
    public const OPTION_TYPE_TEXT     = 'T';

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    /**
     * Setting creation rule
     *
     * @var string
     */
    protected static $optionTypes = [
        self::OPTION_TYPE_BOOLEAN  => \XLite\Model\Attribute::TYPE_CHECKBOX,
        self::OPTION_TYPE_DATE     => \XLite\Model\Attribute::TYPE_TEXT,   // no analog, will be treated as text
        self::OPTION_TYPE_MULTIPLE => \XLite\Model\Attribute::TYPE_SELECT,
        self::OPTION_TYPE_NUMERIC  => \XLite\Model\Attribute::TYPE_TEXT,   // no analog, will be treated as text
        self::OPTION_TYPE_SINGLE   => \XLite\Model\Attribute::TYPE_SELECT,
        self::OPTION_TYPE_TEXT     => \XLite\Model\Attribute::TYPE_TEXT,
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
        return parent::defineColumns() + [
                'fclassId' => [],
            ];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $group   = static::FC_GROUP;
        $options = '( ' . static::getFeatureOptionsSQL() . ' )';

        if (!static::isTableExists('feature_variants')) {
            $options = 'fco.variants';
        }

        return 'fco.fclassid `fclassId`,'
            . 'fco.option_name `name`,'
            . 'fco.orderby `position`,'
            . '"" `product`,'
            . 'fc.class `class`,'
            . "'{$group}' `group`,"
            . "{$options} `options`,"
            . 'fco.option_type `type`';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $prefix = self::getTablePrefix();

        return "{$prefix}feature_classes fc"
            . " INNER JOIN {$prefix}feature_options fco"
            . ' ON fco.`fclassid` = fc.`fclassid`'
            . ' ORDER BY fc.`orderby`';
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get feature options SQL
     *
     * @return string
     */
    public static function getFeatureOptionsSQL()
    {
        $prefix = static::getTablePrefix();

        $featureVariantsLanguageJoinSQL
            = static::callVersionSpecificFunction('getFeatureVariantsLanguageJoinSQL');

        return 'SELECT GROUP_CONCAT('
            . ' fvl.`variant_name` ORDER BY fv.`orderby` ASC, fv.`fvariantid` ASC SEPARATOR "&&"'
            . ' )'
            . " FROM {$prefix}feature_options AS fo"
            . " INNER JOIN {$prefix}feature_variants AS fv"
            . ' ON fv.`foptionid` = fo.`foptionid`'
            . $featureVariantsLanguageJoinSQL
            . ' WHERE fo.`fclassid` = fc.`fclassid`'
            . ' AND fv.`foptionid` = fco.`foptionid`';
    }

    /**
     * Get feature variants language SQL
     *
     * @return string
     */
    public static function getFeatureVariantsLanguageJoinSQL()
    {
        $prefix = static::getTablePrefix();
        $dcl    = Configuration::getDefaultCustomerLanguage();

        return " INNER JOIN {$prefix}feature_variants_lng_{$dcl} AS fvl"
            . ' ON fvl.`fvariantid` = fv.`fvariantid`';
    }

    /**
     * Get feature variants language SQL
     *
     * @return string
     */
    public static function getFeatureVariantsLanguageJoinSQL4_4_x()
    {
        if (
            ($version = static::getPlatformVersion())
            && version_compare($version, '4.4.3') <= 0
        ) {
            // exception for versions <= 4.4.3
            $prefix = static::getTablePrefix();
            $dcl    = Configuration::getDefaultCustomerLanguage();

            return " INNER JOIN {$prefix}feature_variants_lng fvl"
                . ' ON fvl.`fvariantid` = fv.`fvariantid`'
                . " AND fvl.`code` = '{$dcl}'";
        }

        return static::getFeatureVariantsLanguageJoinSQL();
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating product classes');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'type' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeTypeValue($value)
    {
        return static::$optionTypes[$value];
    }

    /**
     * @param $value
     *
     * @return array
     */
    protected function normalizeOptionsValue($value)
    {
        if (!empty($value) && !is_array($value)) {
            $value = static::unserializeLatin1($value);

            if (!empty($value)) {
                $dcl    = Configuration::getDefaultCustomerLanguage();
                $options = [];
                foreach ($value as $option) {
                    $options[] = $option[$dcl] ?? reset($option);
                }
                $value = $options;
            }
        }

        return $value;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $dataset = static::defineDataset();

        return Configuration::isModuleEnabled(Configuration::MODULE_FEATURE_COMPARISON)
            && static::getCellData(
                'SELECT 1'
                . " FROM {$dataset} LIMIT 1"
            );
    }

    // }}} </editor-fold>
}
