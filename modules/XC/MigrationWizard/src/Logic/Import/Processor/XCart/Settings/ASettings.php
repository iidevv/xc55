<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

use XLite\InjectLoggerTrait;

/**
 * Abstract settings
 */
abstract class ASettings extends \XC\MigrationWizard\Logic\Import\Processor\AProcessor
{
    use InjectLoggerTrait;

    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const CONFIG_FIELD_NAME          = 'fieldName';
    public const CONFIG_FIELD_IS_REQUIRED   = 'fieldIsRequired';
    public const CONFIG_FIELD_IS_VIRTUAL    = 'fieldIsVirtual';
    public const CONFIG_FIELD_INITIALIZATOR = 'fieldInitializator';
    public const CONFIG_FIELD_NORMALIZATOR  = 'fieldNormalizator';
    public const CONFIG_FIELD_NORMALIZATOR_SKIP_ESCAPE = 'fieldNormalizatorSkipEscape';
    public const CONFIG_FIELD_CATEGORY      = 'fieldCategory';

    public const CONFIG_SETTING_CAN_CREATE = 'settingCanCreate';
    public const CONFIG_SETTING_MUST_EXIST = 'settingMustExist';

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    /**
     * Configset cache
     *
     * @var array
     */
    protected static $configset = [];

    /**
     * Initializers cache
     *
     * @var array
     */
    protected static $initializators = [];

    /**
     * Normalizators cache
     *
     * @var array
     */
    protected static $normalizators = [];

    /**
     * Virtual fields cache
     *
     * @var array
     */
    protected static $virtualFields = [];

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
            'name'     => [
                self::COLUMN_IS_KEY => true,
            ],
            'category' => [
                self::COLUMN_IS_KEY => true,
            ],
            'value'    => [],
        ];
    }

    /**
     * Define category name
     *
     * @return string
     */
    public static function defineCategoryName()
    {
        $path = explode('\\', get_called_class());

        return array_pop($path);
    }

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [];
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        if (static::getConfigset()) {
            $cat = str_replace('\\', '\\\\', static::defineCategoryName());

            return 'name AS `name`,'
                . "'$cat' AS `category`,"
                . 'value AS `value`';
        }

        return '';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $dataset = '';

        if ($configset = static::getConfigset()) {
            $prefix  = self::getTablePrefix();
            $dataset = "{$prefix}config";

            if (static::hasVirtualFields()) {
                // Use per-field initialization
                $cat     = static::defineCategoryName();
                $sources = [];

                foreach ($configset as $fieldKey => $fieldInfo) {
                    $fieldValue     = static::defineFieldValue($fieldInfo, $fieldKey);
                    $valueCondition = static::defineFieldValueCondition($fieldInfo);

                    if (!empty($fieldInfo[static::CONFIG_FIELD_IS_VIRTUAL])) {
                        $sources[] = "SELECT '{$fieldKey}' as `name`"
                            . ", '$cat' AS `category`"
                            . ", {$fieldValue} as `value`";
                    } else {
                        $fieldset = rtrim(static::defineFieldset(), ' ,');

                        $sources[] = "SELECT {$fieldset}"
                            . " FROM {$dataset}"
                            . " WHERE name='{$fieldKey}'{$valueCondition}";
                    }
                }

                $source = implode(' UNION ', $sources);

                $dataset = "($source) AS CD";
            }
        }

        return $dataset;
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        $filter = '';

        if ($configset = static::getConfigset()) {
            $firstKey  = key($configset);
            $firstInfo = array_shift($configset);

            $filter = static::addFieldCondition([$firstKey => $firstInfo]);

            foreach ($configset as $fieldKey => $fieldInfo) {
                $filter .= static::addFieldCondition([$fieldKey => $fieldInfo], true);
            }
        }

        return $filter;
    }

    /**
     * Define field value
     *
     * @return string
     */
    protected static function defineFieldValue($fieldInfo, $fieldValue)
    {
        if (
            !empty($fieldInfo[static::CONFIG_FIELD_INITIALIZATOR])
            && ($valueInitializer = $fieldInfo[static::CONFIG_FIELD_INITIALIZATOR])
        ) {
            if (empty($fieldInfo[static::CONFIG_FIELD_NORMALIZATOR_SKIP_ESCAPE])) {
                $fieldValue = "'" . static::$valueInitializer() . "'";
            } else {
                $fieldValue =   static::$valueInitializer() ;
            }
        }

        return $fieldValue;
    }

    /**
     * Define field value condition
     *
     * @return string
     */
    protected static function defineFieldValueCondition($fieldInfo)
    {
        $valueCondition = '';

        if (!empty($fieldInfo[static::CONFIG_FIELD_IS_REQUIRED])) {
            $valueCondition = " AND `value` <> ''";
        }

        return $valueCondition;
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config');
    }

    /**
     * @return array
     */
    protected static function getConfigset()
    {
        $class = get_called_class();

        if (!isset(static::$configset[$class])) {
            $defineConfigset           = static::getVersionSpecificName($class, 'defineConfigset');
            static::$configset[$class] = static::$defineConfigset();

            static::processConfigset($class);
        }

        return static::$configset[$class];
    }

    protected static function getInitializers()
    {
        $class = get_called_class();

        return static::$initializators[$class] ?? [];
    }

    protected static function getNormalizers()
    {
        $class = get_called_class();

        return static::$normalizators[$class] ?? [];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeNameValue($value)
    {
        $config = static::getConfigset();

        return $config[$value][static::CONFIG_FIELD_NAME];
    }

    /**
     * Normalize 'category' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeCategoryValue($value)
    {
        return str_replace('\\', '\\', static::defineCategoryName());
    }

    /**
     * Normalize 'value' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeValueValue($value, $currentRowData)
    {
        $normalizers = static::getNormalizers();

        return !empty($currentRowData['name']) && !empty($normalizers[$currentRowData['name']])
            ? $this->{$normalizers[$currentRowData['name']]}($value)
            : $value;
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity or null
     */
    protected function createModel(array $data)
    {
        if ($this->createRule !== static::CONFIG_SETTING_MUST_EXIST) {
            return parent::createModel($data);
        }

        $data['name'] = $this->normalizeNameValue($data['name']);

        unset($data['value']); // to skip values from logging

        $this->getLogger('migration_errors')->debug('', [
            'Error' => 'Setting cannot be found!',
            'Data'  => $data,
            'SQL'   => $this->getRecordset()->getLastQuerySQL(),
        ]);

        \XC\MigrationWizard\Logic\Migration\Wizard::registerTransferDataError();

        return null;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    public static function hasVirtualFields()
    {
        $class = get_called_class();

        if (!isset(static::$virtualFields[$class]) && ($configset = static::getConfigset())) {
            foreach ($configset as $fieldKey => $fieldInfo) {
                if (!empty($fieldInfo[static::CONFIG_FIELD_IS_VIRTUAL])) {
                    if (!isset(static::$virtualFields[$class])) {
                        static::$virtualFields[$class] = [];
                    }

                    static::$virtualFields[$class][] = $fieldKey;
                }
            }
        }

        return !empty(static::$virtualFields[$class]);
    }

    /**
     * @return bool
     */
    protected static function checkTransferableDataPresent()
    {
        $from  = static::defineDataset();
        $where = static::defineDatafilter();

        if (!$from) {
            return false;
        }

        return (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$from}"
            . (!empty($where) ? " WHERE {$where}" : '')
        );
    }

    /**
     * Return TRUE if processor has heading row
     *
     * @return boolean
     */
    public static function hasHeadingRow()
    {
        return false;
    }

    /**
     * Process configset
     *
     * @return array
     */
    protected static function processConfigset($class)
    {
        $result = [];

        foreach (static::$configset[$class] as $key => $field) {
            $fieldInfo = $field;

            $fieldName        = \XLite\Core\Converter::convertToCamelCase($field[static::CONFIG_FIELD_NAME]);
            $valueInitializer = static::getVersionSpecificName($class, "initField{$fieldName}Value");

            if (method_exists($class, $valueInitializer)) {
                $fieldInfo[static::CONFIG_FIELD_INITIALIZATOR]                     = $valueInitializer;
                static::$initializators[$class][$field[static::CONFIG_FIELD_NAME]] = $valueInitializer;
                static::$initializators[$class][$key]                              = $valueInitializer;
            }

            $valueNormalizer = static::getVersionSpecificName($class, "normalizeField{$fieldName}Value");

            if (method_exists($class, $valueNormalizer)) {
                $fieldInfo[static::CONFIG_FIELD_NORMALIZATOR]                     = $valueNormalizer;
                static::$normalizators[$class][$field[static::CONFIG_FIELD_NAME]] = $valueNormalizer;
                static::$normalizators[$class][$key]                              = $valueNormalizer;
            }

            $result[$key] = $fieldInfo;
        }

        static::$configset[$class] = $result;
    }

    /**
     * Add field condition
     *
     * @param array $field
     * @param bool  $withOR
     * @param bool  $fieldNameAsCondition
     *
     * @return string
     */
    protected static function addFieldCondition($field, $withOR = false, $fieldNameAsCondition = false)
    {
        $fieldName = key($field);
        $fieldInfo = $field[$fieldName];

        if (
            $fieldNameAsCondition
            && $fieldInfo[static::CONFIG_FIELD_NAME]
        ) {
            $fieldName = $fieldInfo[static::CONFIG_FIELD_NAME];
        }

        $valueCondition = '';
        if (
            !empty($fieldInfo[static::CONFIG_FIELD_IS_REQUIRED])
            && empty($fieldInfo[static::CONFIG_FIELD_IS_VIRTUAL]) // skip virtual fields here, other conditions must be for virtuals in where
        ) {
            $valueCondition = " AND value <> ''";
        }

        $valueCondition .= static::addWorthlessFieldCondition($fieldInfo[static::CONFIG_FIELD_NAME] ?? $fieldName);

        $or = $withOR ? ' OR ' : '';

        return "{$or}(name = '{$fieldName}'{$valueCondition})";
    }

    /**
     * Ignore Some Fields That Have Worthless Values. Are Useful For Shippings
     *
     * @param string $field_name
     *
     * @return string
     */
    protected static function addWorthlessFieldCondition($field_name)
    {
        return "";
    }

    // }}} </editor-fold>
}
