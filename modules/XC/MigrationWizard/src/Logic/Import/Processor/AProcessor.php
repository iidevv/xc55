<?php
// phpcs:ignoreFile
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;
use XCart\Domain\ModuleManagerDomain;
use XCart\Extender\Mapping\Extender;
use XLite\InjectLoggerTrait;

/**
 * Abstract Processor
 *
 * @Extender\Mixin
 */
abstract class AProcessor extends \XLite\Logic\Import\Processor\AProcessor
{
    use \XLite\Core\Cache\ExecuteCachedTrait;
    use InjectLoggerTrait;

    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const COLUMN_IS_OPTIONAL = 'isOptional';
    public const COLUMN_CONFIG_NAME = 'configName';

    public const COLUMN_IS_EMULATION = 'isEmulation';
    public const COLUMN_IS_VIRTUAL   = 'isVirtual';
    public const COLUMN_PARSE_IMAGES = 'parse_before_normalization';

    public const REGISTRY_SOURCE = 'sourceID';
    public const REGISTRY_RESULT = 'resultID';
    public const REGISTRY_ENTITY = 'entity';

    public const REGISTRY_REGISTER_NOT_REQUIRED = 'notRequired';
    public const REGISTRY_REGISTER_SUCCESS      = 'registerSuccess';
    public const REGISTRY_REGISTER_FAILED       = 'registerFailed';

    public const GENERATOR_PLACEHOLDER = \XC\MigrationWizard\Logic\Import\Recordset::GENERATOR_PLACEHOLDER;

    public const SINGLE_TYPE_IMAGES   = 1;
    public const MULTIPLE_TYPE_IMAGES = 3;

    public const IMAGES_LOCATION_FS = 'FS';
    public const IMAGES_LOCATION_DB = 'DB';

    public const DEMO_MIGRATION_COUNT = 20;

    protected const CACHE_TTL = 15552000; // 180 days

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    /**
     * @var \XLite\Logic\Import\Importer
     */
    protected static $staticImporter;

    /**
     * @var array
     */
    protected static $staticCache = [];

    /**
     * @var array
     */
    protected static $imageTypes = [
        'A' => self::SINGLE_TYPE_IMAGES,   // Banner System images
        'B' => self::SINGLE_TYPE_IMAGES,   // Affiliate graphic banners
        'C' => self::SINGLE_TYPE_IMAGES,   // Category icons
        'D' => self::MULTIPLE_TYPE_IMAGES, // Detailed images
        'F' => self::SINGLE_TYPE_IMAGES,   // Product class images
        'G' => self::SINGLE_TYPE_IMAGES,   // Language icons
        'L' => self::SINGLE_TYPE_IMAGES,   // Library items for Affiliate media-rich banners
        'M' => self::SINGLE_TYPE_IMAGES,   // Manufacturer logos
        'P' => self::SINGLE_TYPE_IMAGES,   // Product images
        'S' => self::SINGLE_TYPE_IMAGES,   // Special offer images
        'T' => self::SINGLE_TYPE_IMAGES,   // Product thumbnails
        'W' => self::SINGLE_TYPE_IMAGES,   // Variant images
        'Z' => self::SINGLE_TYPE_IMAGES,   // Magnifier
    ];

    /**
     * @var \XLite\Model\AEntity
     */
    protected $currentlyProcessingModel;

    /**
     * @var \XLite\Logic\Import\Processor\AProcessor
     */
    protected $subprocessor;

    /**
     * @var array
     */
    protected static $modelsToBeFlushed = [];

    // }}} </editor-fold>

    public function __construct(\XLite\Logic\Import\Importer $importer)
    {
        parent::__construct($importer);

        if (self::$staticImporter === null) {
            self::$staticImporter = $importer;
        }
    }

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePreProcessors()
    {
        return [];
    }

    /**
     * Define extra processors
     *
     * @return array
     */
    protected static function definePostProcessors()
    {
        return [];
    }

    /**
     * Define sub processors
     *
     * @return array
     */
    public static function defineSubProcessors()
    {
        return [];
    }

    /**
     * Define columns which fetched as id of main entity
     * This ones should be normalized before calculate checksum
     *
     * @return array
     */
    protected static function defineColumnsNeedNormalizeForHash()
    {
        return [];
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return [];
    }

    /**
     * Define fieldset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        return '*';
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        return '';
    }

    /**
     * Define filter SQL
     *
     * @return string
     */
    public static function defineDatafilter()
    {
        return '';
    }

    /**
     * Define Fields Used For Sql Order By
     *
     * @return array
     */
    public static function defineDatasorter()
    {
        return [];
    }

    /**
     * Define ID generator data
     *
     * @return array
     */
    public static function defineIdGenerator()
    {
        return [];
    }

    /**
     * Define Fields Which Will Be Used For Count
     *
     * @return string
     */
    public static function defineCountFields()
    {
        return '';
    }

    /**
     * Define Fields Used For Sql Group By
     *
     * @return array
     */
    public static function defineDatagrouper()
    {
        return [];
    }

    /**
     * Define registry entry
     *
     * @return array
     */
    public static function defineRegistryEntry()
    {
        return [];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * @var \XC\MigrationWizard\Logic\Migration\Wizard
     */
    protected static $migrationWizard;

    /**
     * Get migration wizard object
     *
     * @return \XC\MigrationWizard\Logic\Migration\Wizard
     */
    public static function getMigrationWizard()
    {
        if (self::$migrationWizard === null) {
            self::$migrationWizard = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance();
        }

        return self::$migrationWizard;
    }

    /**
     * @var bool|\XC\MigrationWizard\Logic\Migration\Step\Connect
     */
    protected static $stepConnect;

    /**
     * @return bool|\XC\MigrationWizard\Logic\Migration\Step\Connect
     */
    public static function getStepConnect()
    {
        if (self::$stepConnect === null) {
            self::$stepConnect = self::getMigrationWizard()->getStep('Connect');
        }

        return self::$stepConnect;
    }

    /**
     * Get connection
     *
     * @return bool|\PDO
     */
    public static function getConnection()
    {
        return self::getStepConnect()->getConnection();
    }

    /**
     * Get database name
     *
     * @return string
     */
    public static function getDatabase()
    {
        return self::getStepConnect()->getDatabase();
    }

    /**
     * Get tables prefix
     *
     * @return string
     */
    public static function getTablePrefix()
    {
        return self::getStepConnect()->getPrefix();
    }

    /**
     * Get site URL
     *
     * @return string
     */
    public static function getSiteUrl()
    {
        return self::getStepConnect()->getSiteUrl();
    }

    /**
     * Get site URL
     *
     * @return string
     */
    public static function getSitePath()
    {
        return self::getStepConnect()->getSitePath();
    }

    /**
     * Get Source Local Flag
     *
     * @return boolean
     */
    public static function isSiteRemote()
    {
        return ! self::getStepConnect()->isSourceSiteLocal();
    }

    /**
     * @var \PDOStatement[]
     */
    protected static $statements = [];

    /**
     * @param string $query
     *
     * @return bool|\PDOStatement
     */
    public static function getPreparedPDOStatement($query)
    {
        $key = md5($query);
        if (!array_key_exists($key, self::$statements)) {
            $connection = self::getConnection();

            static::$statements[$key] = $connection
                ? $connection->prepare($query)
                : false;
        }

        return static::$statements[$key];
    }

    /**
     * @param string $query
     * @param        $mode
     *
     * @return mixed
     */
    public static function getData($query, $mode = \PDO::FETCH_ASSOC)
    {
        $connection = self::getConnection();

        if ($connection) {
            $PDOStatement = $connection->query($query);
            if ($PDOStatement) {
                return $PDOStatement->fetch($mode);
            }
        }

        return false;
    }

    /**
     * @param string $query
     *
     * @return mixed
     */
    public static function getKeyValueData($query)
    {
        $connection = self::getConnection();

        if ($connection) {
            $PDOStatement = $connection->query($query);
            if ($PDOStatement) {
                return $PDOStatement->fetchAll(\PDO::FETCH_KEY_PAIR);
            }
        }

        return false;
    }

    /**
     * @param string $query
     *
     * @return mixed
     */
    public static function getColumnData($query)
    {
        $connection = self::getConnection();

        if ($connection) {
            $PDOStatement = $connection->query($query);
            if ($PDOStatement) {
                return $PDOStatement->fetchAll(\PDO::FETCH_COLUMN);
            }
        }

        return false;
    }

    /**
     * @param string $query
     *
     * @return mixed
     */
    public static function getCellData($query)
    {
        $connection = self::getConnection();

        if ($connection) {
            $PDOStatement = $connection->query($query);
            if ($PDOStatement) {
                return $PDOStatement->fetch(\PDO::FETCH_COLUMN);
            }
        }

        return false;
    }

    /**
     * Return a list of pre-processors with data
     *
     * @return array
     */
    public static function getPreProcessors()
    {
        return static::definePreProcessors();
    }

    /**
     * Return a list of post-processors with data
     *
     * @return array
     */
    public static function getPostProcessors()
    {
        return static::definePostProcessors();
    }

    /**
     * Return not installed modules array or FALSE otherwise
     */
    public static function getNotInstalledModules(): array
    {
        $result = [];

        if ($subprocessors = static::getAllProcessorsWithData()) {
            foreach ($subprocessors as $subprocessor) {
                if ($modules = $subprocessor::getNotInstalledModules()) {
                    $result += $modules;
                }
            }
        } else {
            if (($modules = static::defineRequiredModules()) && static::hasTransferableData()) {
                $moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
                foreach ($modules as $moduleName) {
                    [$author, $name] = explode('\\', $moduleName);
                    $module = $moduleManagerDomain->getModule("$author-$name");
                    if (!$module) {
                        $result[$moduleName] = $moduleName;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Return disabled modules array or FALSE otherwise
     */
    public static function getDisabledModules(): array
    {
        $result = [];

        if ($subprocessors = static::getAllProcessorsWithData()) {
            foreach ($subprocessors as $subprocessor) {
                if ($modules = $subprocessor::getDisabledModules()) {
                    $result += $modules;
                }
            }
        } else {
            if (($modules = static::defineRequiredModules()) && static::hasTransferableData()) {
                $moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
                foreach ($modules as $moduleName) {
                    [$author, $name] = explode('\\', $moduleName);
                    $module = $moduleManagerDomain->getModule("$author-$name");
                    if ($module && !$module['isEnabled']) {
                        $result[$moduleName] = $moduleName;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get UID by fields concatenation
     *
     * @param array $fields
     *
     * @return string
     */
    public static function getConcatUidSQL(array $fields)
    {
        $result = '';

        if (!empty($fields)) {
            $index = 0;

            $list = implode(', ', array_map(static function ($item) use (&$index) {
                if ($index === 0) {
                    $index += 1;

                    return $item;
                }

                return "CONCAT('(', CONCAT({$item}, ')'))";
            }, $fields));

            $result = "CONCAT_WS(' ', {$list})";
        }

        return $result;
    }

    /**
     * Return language specific names for the given fields
     *
     * @param array $fields
     * @param array $languages
     *
     * @return string
     */
    public static function getLanguageFieldsSQLfor(array $fields, array $languages)
    {
        $list = '';

        foreach ($fields as $field => $alias) {
            foreach ($languages as $lng) {
                $field = is_numeric($field) ? $alias : $field;

                $list .= "{$field} {$alias}_{$lng}, ";
            }
        }

        return $list;
    }

    /**
     * Return clean URLs fields SQL for the given resource type
     *
     * @param string $field_name
     *
     * @return string
     */
    public static function getCleanURLsFieldsSQLfor($field_name = 'cleanURL')
    {
        return Configuration::areCleanURLsEnabled()
            ? "IF(cu.`clean_url` IS NULL, '', cu.`clean_url`) {$field_name}, cu.resource_id cleanURLId, cu.resource_type cleanURLType, "
            : '';
    }

    /**
     * Return clean URLs join SQL for the given resource type
     *
     * @param string $resource_type
     * @param string $resource_id
     * @param string $joinType
     *
     * @return string
     */
    public static function getCleanURLsJoinSQLfor($resource_type, $resource_id, $joinType = 'INNER')
    {
        $joinSQL = '';

        if (Configuration::areCleanURLsEnabled()) {
            $prefix = self::getTablePrefix();

            $joinSQL = "{$joinType} JOIN {$prefix}clean_urls cu"
                . " ON cu.resource_type = '{$resource_type}'"
                . " AND cu.resource_id = {$resource_id}";
        }

        return $joinSQL;
    }

    /**
     * Return a list of processors with data from the given list
     *
     * @param array $processors
     *
     * @return array
     */
    public static function getProcessorsWithDataFrom($processors, $definer = '')
    {
        $result = [];

        if (!empty($processors)) {
            foreach ($processors as $processor) {
                if (
                    !empty($definer)
                    && ($list = $processor::$definer())
                    && !empty($list)
                ) {
                    $result = array_merge($result, $processor::getProcessorsWithDataFrom($list, $definer));
                } elseif ($processor::hasTransferableData()) {
                    $result[] = $processor;
                }
            }
        }

        return $result;
    }

    /**
     * Return a list of pre-processors with data
     *
     * @return array
     */
    protected static function getPreProcessorsWithData()
    {
        static $result = [];

        $class = get_called_class();

        if (!isset($result[$class])) {
            $result[$class] = static::getProcessorsWithDataFrom(static::definePreProcessors(), 'definePreProcessors');
        }

        return $result[$class];
    }

    /**
     * Return a list of post-processors with data
     *
     * @return array
     */
    protected static function getPostProcessorsWithData()
    {
        static $result = [];

        $class = get_called_class();

        if (!isset($result[$class])) {
            $result[$class] = static::getProcessorsWithDataFrom(static::definePostProcessors(), 'definePostProcessors');
        }

        return $result[$class];
    }

    /**
     * Return a list of sub-processors with data
     *
     * @return array
     */
    public static function getSubProcessorsWithData()
    {
        static $result = [];

        $class = get_called_class();

        if (!isset($result[$class])) {
            $result[$class] = static::getProcessorsWithDataFrom(static::defineSubProcessors(), 'defineSubProcessors');
        }

        return $result[$class];
    }

    /**
     * Return a list of processors with data
     *
     * @return array
     */
    public static function getAllProcessorsWithData()
    {
        $result = array_merge(
            [],
            static::getPreProcessorsWithData(),
            static::getSubProcessorsWithData(),
            static::getPostProcessorsWithData()
        );

        return $result;
    }

    /**
     * Return version specific function name
     *
     * @return string
     */
    public static function getPlatformVersion()
    {
        $result = 'Unknown';

        if (
            ($requirements = static::getMigrationWizard()->getStep('CheckRequirements'))
            && ($requirement = $requirements->getRequirement())
            && method_exists($requirement, 'getVersion')
        ) {
            $result = $requirement->getVersion();
        }

        return $result;
    }

    /**
     * Return version specific function name
     *
     * @param object|string $object
     * @param string        $function_name
     *
     * @return string
     */
    protected static function getVersionSpecificName($object, $function_name)
    {
        $result = $function_name;

        if (($version = static::getPlatformVersion())) {
            $base_version_array = $version_array = array_slice(explode('.', $version), 0, 3);

            // Replace last part with 'x'
            array_pop($base_version_array);
            array_push($base_version_array, 'x');

            // Base version specific name
            $base_version_func_name = $function_name . implode('_', $base_version_array);

            if (method_exists($object, $base_version_func_name)) {
                $result = $base_version_func_name;
            }

            // Target version specific name
            $target_version_func_name = $function_name . implode('_', $version_array);

            if (method_exists($object, $target_version_func_name)) {
                $result = $target_version_func_name;
            }
        }

        return $result;
    }

    /**
     * Return entry from Migration Wizard Registry
     *
     * @param string  $class
     * @param integer $sourceId
     *
     * @return \XC\MigrationWizard\Model\MigrationRegistryEntry
     */
    protected static function getEntryFromRegistryByClassAndSourceId($class, $sourceId)
    {
        $registry = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRegistry')
            ->findOneBy(['name' => $class]);

        $res = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRegistryEntry')
            ->findOneBy(['registry' => $registry, 'sourceId' => $sourceId]);

        if (!$res && $registry) {
            // Searching In Static Php Cache / Flush And Obtain Again
            $_cacheValueKey = static::getCacheKeyOfModelData(['registry' => $registry, 'sourceId' => $sourceId]);

            if (isset(static::$modelsToBeFlushed[$_cacheValueKey])) {
                // The Entity Is Managed But Not Flushed Yet. Do It And Obtain Again
                static::databaseGetEmFlush();

                $res =  \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRegistryEntry')
                    ->findOneBy(['registry' => $registry, 'sourceId' => $sourceId]);
            }
        }

        return $res;
    }

    /**
     * Return entry from Migration Wizard Registry
     *
     * @param string  $class
     * @param integer $resultId
     *
     * @return \XC\MigrationWizard\Model\MigrationRegistryEntry
     */
    protected static function getEntryFromRegistryByClassAndResultId($class, $resultId)
    {
        $registry = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRegistry')
            ->findOneBy(['name' => $class]);

        return \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRegistryEntry')
            ->findOneBy(['registry' => $registry, 'resultId' => $resultId]);
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return '';
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    /**
     * Get ids of products for demo migration
     *
     * @return array
     */
    public static function getDemoProductIds()
    {
        $demoProductIds = [];

        if (
            static::isDemoModeMigration()
            && $transferData = static::getMigrationWizard()->getStep('DetectTransferableData')
        ) {
            $demoProductIds = $transferData->getDemoProductIds();
        }

        return $demoProductIds;
    }

    /**
     * Get ids of orders for demo migration
     *
     * @return array
     */
    public static function getDemoOrderIds()
    {
        $demoOrderIds = [];

        if (
            static::isDemoModeMigration()
            && $transferData = static::getMigrationWizard()->getStep('DetectTransferableData')
        ) {
            $demoOrderIds = $transferData->getDemoOrderIds();
        }

        return $demoOrderIds;
    }

    /**
     * Get ids of users for demo migration
     *
     * @return array
     */
    public static function getDemoUserIds()
    {
        $demoUserIds = [];

        if (
            static::isDemoModeMigration()
            && $transferData = static::getMigrationWizard()->getStep('DetectTransferableData')
        ) {
            $demoUserIds = $transferData->getDemoUserIds();
        }

        return $demoUserIds;
    }

    /**
     * Check if should use entity cache
     *
     * @return string
     */
    protected static function isUseEntityCache()
    {
        $result = false;

        if ($transferData = static::getMigrationWizard()->getStep('TransferData')) {
            $result = $transferData->isUseEntityCache();
        }

        return $result;
    }

    /**
     * Check if should migrate only few entities
     *
     * @return string
     */
    protected static function isDemoModeMigration()
    {
        $result = false;

        if ($transferData = static::getMigrationWizard()->getStep('DetectTransferableData')) {
            $result = $transferData->isDemoMode();
        }

        return $result;
    }

    /**
     * Get orders start date
     *
     * @return int
     */
    protected static function getOrdersStartDate()
    {
        $result = false;

        if ($transferData = static::getMigrationWizard()->getStep('TransferData')) {
            $result = $transferData->getOrdersStartDate();
        }

        return $result;
    }

    /**
     * Check if import running right now
     *
     * @return string
     */
    public static function isImportRunning()
    {
        $result = false;

        if ($transferData = static::getMigrationWizard()->getStep('TransferData')) {
            $result = $transferData->isImportRunning();
        }

        return $result;
    }

    /**
     * Call version specific function with the given arguments
     *
     * @param string $function  function name
     * @param mixed  $context   function context (class name or object)
     * @param mixed  $arguments function arguments array
     *
     * @return mixed
     */
    public static function callVersionSpecificFunction($function, $context = null, array $arguments = [])
    {
        if (empty($context)) {
            $context = get_called_class();
        }

        $version_specific_function = static::getVersionSpecificName($context, $function);

        return call_user_func_array([$context, $version_specific_function], $arguments);
    }

    /**
     * Return TRUE if source data exists
     *
     * @return boolean
     */
    public static function hasTransferableData()
    {
        $context = get_called_class();

        if (static::hasMigrationCache('hasTransferableData', $context)) {
            return static::getMigrationCache('hasTransferableData', $context);
        }

        $result = static::checkTransferableDataPresent();

        static::setMigrationCache('hasTransferableData', $context, $result);

        return $result;
    }

    /**
     * @return bool
     */
    protected static function checkTransferableDataPresent()
    {
        return true;
    }

    /**
     * Return missing modules array or FALSE otherwise
     *
     * @return array or false
     */
    public static function hasMissingModules()
    {
        $result = [];

        $missing  = static::getNotInstalledModules();
        $disabled = static::getDisabledModules();

        $result += $missing += $disabled;

        return !empty($result) ? $result : false;
    }

    /**
     * Return TRUE if processor has pre-processors
     *
     * @return boolean
     */
    public static function hasPreProcessors()
    {
        return ($result = static::definePreProcessors()) && !empty($result);
    }

    /**
     * Return TRUE if processor has post-processors
     *
     * @return boolean
     */
    public static function hasPostProcessors()
    {
        return ($result = static::definePostProcessors()) && !empty($result);
    }

    /**
     * Return TRUE if processor has subprocessors
     *
     * @return boolean
     */
    public static function hasSubProcessors()
    {
        return ($result = static::defineSubProcessors()) && !empty($result);
    }

    /**
     * Return TRUE if processor has subprocessors with data
     *
     * @return boolean
     */
    public static function hasSubProcessorsWithData()
    {
        return ($result = static::getSubProcessorsWithData()) && !empty($result);
    }

    /**
     * Return TRUE if processor has a heading row
     *
     * @return boolean
     */
    public static function hasHeadingRow()
    {
        return count(static::defineSubProcessors()) === 0;
    }

    /**
     * Get Actual Auto_increment Even For Mysql8 With https://dev.mysql.com/doc/refman/8.0/en/server-system-variables.html#sysvar_information_schema_stats_expiry
     *
     * @param string $table_name
     *
     * @return int
     */
    public static function getTrueAutoIncrement($table_name)
    {
        $id = static::getKeyValueData("SHOW CREATE TABLE $table_name");

        if (
            !empty($id)
            && !empty($id[$table_name])
            && preg_match('/\) ENGINE=.*AUTO_INCREMENT=(\d+).*$/', $id[$table_name], $arr) // m/s/i Isn't Used Intentionaly. $ Is Used Intentionaly In The End
            && !empty($arr[1])
        ) {
            return $arr[1];
        }

        // Fallback To Cached Variant
        $id = static::getCellData(
            'SELECT AUTO_INCREMENT'
            . ' FROM INFORMATION_SCHEMA.TABLES'
            . ' WHERE TABLE_SCHEMA = DATABASE()'
            . " AND TABLE_NAME = '$table_name'"
        );
        return $id;
    }

    /**
     * Return TRUE if table exists in database
     *
     * @param string $table_name
     *
     * @return boolean
     */
    public static function isTableExists($table_name)
    {
        static $tables = [];

        $database_name = static::getDatabase();
        $tp            = static::getTablePrefix();

        if (!isset($tables[$database_name])) {
            $tables[$database_name] = [];
        }

        if (!isset($tables[$database_name][$table_name])) {
            $tables[$database_name][$table_name] = false;

            $sql = "SELECT (COUNT(*) > 0) AS result"
                . " FROM information_schema.TABLES"
                . " WHERE TABLE_SCHEMA = '{$database_name}'"
                . " AND TABLE_NAME = '{$tp}{$table_name}'";

            $query = static::getConnection()->query($sql);

            if (!empty($query)) {
                $tables[$database_name][$table_name] = (bool) $query->fetchColumn();
            }
        }

        return $tables[$database_name][$table_name];
    }

    /**
     * Return TRUE if column exists in given the table
     *
     * @param string $table_name
     * @param string $column_name
     *
     * @return boolean
     */
    public static function isTableColumnExists($table_name, $column_name)
    {
        static $columns = [];

        $database_name = static::getDatabase();
        $tp            = static::getTablePrefix();

        if (!isset($columns[$database_name][$table_name][$column_name])) {
            $columns[$database_name][$table_name][$column_name] = false;

            $sql = "SELECT (COUNT(*) > 0) AS result"
                . " FROM information_schema.COLUMNS"
                . " WHERE TABLE_SCHEMA = '{$database_name}'"
                . " AND TABLE_NAME = '{$tp}{$table_name}'"
                . " AND COLUMN_NAME = '{$column_name}'";

            $query = static::getConnection()->query($sql);

            if (!empty($query)) {
                $columns[$database_name][$table_name][$column_name] = (bool) $query->fetchColumn();
            }
        }

        return $columns[$database_name][$table_name][$column_name];
    }

    /**
     * Return TRUE if entry needs to be registered in the Migration Wizard Registry
     *
     * @return boolean
     */
    public function isModelRegistrationRequired()
    {
        return (bool) static::defineRegistryEntry();
    }

    /**
     * Check if should not add checksum to hash
     *
     * @param $data
     *
     * @return bool
     */
    protected function shouldSkipChecksumReplace($data)
    {
        $result = false;

        if (
            $this instanceof \XC\MigrationWizard\Logic\Import\Processor\XCart\CategoryImages
            || $this instanceof \XC\MigrationWizard\Logic\Import\Processor\XCart\ProductImages
            || $this instanceof \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductVariantsImages
            || $this instanceof \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ManufacturersImages
        ) {
            $model = $this->detectModel($data);

            $result = is_null($model);
        }

        return $result;
    }

    /**
     * Calculate checksum of provided raw data
     *
     * @param array $data
     *
     * @return string
     */
    protected function calculateChecksum($data)
    {
        return md5(serialize($data));
    }

    /**
     * Check if entity is not needed to migrate
     *
     * @param $data
     *
     * @return bool
     */
    protected function checkEntityChecksum($data)
    {
        $result = false;

        if (isset($data['xc4EntityId'])) {
            $data = $this->prepareRawDataForChecksum($data);
            $entitySumHash = static::getFromFileCache([$data['xc4EntityId'], get_called_class()]);

            if ($entitySumHash) {
                $result = $this->calculateChecksum($data) == $entitySumHash;
            }
        }

        return $result;
    }

    /**
     * Replace checksum value for provided data
     *
     * @param $data
     */
    protected function replaceEntityChecksum($data)
    {
        if (isset($data['xc4EntityId'])) {
            $data = $this->prepareRawDataForChecksum($data);
            static::saveInFileCache([$data['xc4EntityId'], get_called_class()], $this->calculateChecksum($data));
        }
    }

    /**
     * Prepare raw data for getting hash
     *
     * @param $data
     */
    protected function prepareRawDataForChecksum($data)
    {
        foreach (static::defineColumnsNeedNormalizeForHash() as $column) {
            if (isset($data[$column])) {
                $methodName = 'normalize' . ucfirst($column) . 'Value';
                if (method_exists($this, $methodName)) {
                    $data[$column] = $this->{$methodName}($data[$column]);
                }
            }
        }

        if ($this instanceof \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\Reviews) {
            unset($data['additionDate']);
        }

        return $data;
    }

    // }}} </editor-fold>

    // {{{ Data helpers <editor-fold desc="Data helpers" defaultstate="collapsed">

    /**
     * Deals With Error Notice: Unserialize(): Error At Offset 284 Of 379 Bytes In
     *
     * @param string $in_ser_data
     *
     * @return string|bool
     */
    protected static function unserializeLatin1($in_ser_data)
    {
        $unserialized_data = @unserialize($in_ser_data);

        if ($unserialized_data === false) {
            // The Following Regex Based Replacement Will Only Be Effective In Remedying Byte Counts, Nothing More.
            // https://stackoverflow.com/a/55566407/6700695
            $in_ser_data = preg_replace_callback('!s:(\d+):"(.*?)";!s', static function ($match) {
                return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
            }, $in_ser_data);

            $unserialized_data = unserialize($in_ser_data);
        }

        return $unserialized_data;
    }

    /**
     * Returns translated valid images path
     *
     * @param array $data
     *
     * @return string
     */
    protected static function getTranslatedImagePath($data)
    {
        $data['image_path'] = str_replace('./', '/', $data['image_path']);

        $result = str_replace($data['filename'], rawurlencode($data['filename']), $data['image_path']);

        return $result;
    }

    /**
     * Get files counts
     *
     * @return array
     */
    public function getCounts()
    {
        if (!$this->isMigrationProcessor()) {
            return parent::getCounts();
        }

        return $this->getRunDisplayCounts();
    }

    /**
     * Get files counts
     *
     * @return array
     */
    public function getRunDisplayCounts($_count_mode = '')
    {
        if ($this->countCache === null) {
            $this->countCache = [];

            $recordset = $this->getRecordset();

            if ($recordset) {
                $this->countCache[$recordset->getPathname()] = $recordset->count($_count_mode);
            }

            if (($subprocessors = static::getSubProcessorsWithData()) && !empty($subprocessors)) {
                foreach ($subprocessors as $spcname) {
                    $subprocessor = new $spcname($this->importer);

                    $subrecordset = $subprocessor->getRecordset();

                    if ($subrecordset) {
                        $this->countCache[$subrecordset->getPathname()] = $subrecordset->count($_count_mode);
                    }
                }
            }
        }

        return $this->countCache;
    }

    /**
     * Return TRUE if processor is an instance of Migration Wizard importer
     *
     * @see \XC\MigrationWizard\Logic\Import\Importer
     *
     * @return boolean
     */
    public function isMigrationProcessor()
    {
        return $this->importer instanceof \XC\MigrationWizard\Logic\Import\Importer;
    }

    /**
     * Check valid state of processor
     *
     * @return boolean
     */
    public function isValid()
    {
        if (!$this->isMigrationProcessor()) {
            return parent::isValid();
        }

        return is_object($this->getRecordset());
    }

    /**
     * Check valid state of processor
     *
     * @return boolean
     */
    public function isEof()
    {
        if (!$this->isMigrationProcessor()) {
            return parent::isEof();
        }

        return !$this->isValid() || $this->recordset->eof();
    }

    /**
     * Define recordset object
     *
     * @return mixed \XC\MigrationWizard\Logic\Import\Recordset or FALSE
     */
    public function defineRecordset(\XC\MigrationWizard\Logic\Import\Processor\AProcessor $processor)
    {
        if (!$processor::hasTransferableData()) {
            return false;
        }

        $datasetDefiner    = static::getVersionSpecificName($processor, 'defineDataset');
        $fieldsetDefiner   = static::getVersionSpecificName($processor, 'defineFieldset');
        $datafilterDefiner = static::getVersionSpecificName($processor, 'defineDatafilter');
        $datasorterDefiner = static::getVersionSpecificName($processor, 'defineDatasorter');
        $generatorDefiner  = static::getVersionSpecificName($processor, 'defineIdGenerator');
        $countFieldsDefiner = static::getVersionSpecificName($processor, 'defineCountFields');
        $datagrouperDefiner = static::getVersionSpecificName($processor, 'defineDatagrouper');

        $dataset    = $processor->$datasetDefiner();
        $fieldset   = $processor->$fieldsetDefiner();
        $datafilter = $processor->$datafilterDefiner();
        $datasorter = $processor->$datasorterDefiner();
        $generator  = $processor->$generatorDefiner();
        $count_fields  = $processor->$countFieldsDefiner();
        $datagrouper = $processor->$datagrouperDefiner();

        return new \XC\MigrationWizard\Logic\Import\Recordset($processor, $dataset, $fieldset, $datafilter, $datasorter, $generator, $count_fields, $datagrouper);
    }

    /**
     * Get clean URL
     *
     * @param string $type
     * @param string $url
     *
     * @return string
     */
    public function getCleanUrl($type, $url)
    {
        if (static::hasMigrationCache('storage', 'cleanURLTypes')) {
            $types = static::getMigrationCache('storage', 'cleanURLTypes');
        } else {
            $prefix = self::getTablePrefix();
            $types  = static::getKeyValueData(
                'SELECT name, value'
                . " FROM {$prefix}config"
                . ' WHERE name'
                . ' LIKE "clean_urls_ext_%"'
            );

            static::setMigrationCache('storage', 'cleanURLTypes', $types);
        }

        $key = 'clean_urls_ext_' . $type;

        return $url . ((isset($types[$key]) && $types[$key] !== '/') ? $types[$key] : '');
    }

    /**
     * Return images location by the given type
     *
     * @param string $type
     *
     * @return string
     */
    public function getImagesLocationByType($type)
    {
        if (static::hasMigrationCache('storage', 'imagesLocations')) {
            $locations = static::getMigrationCache('storage', 'imagesLocations');
        } else {
            $prefix    = static::getTablePrefix();
            $locations = static::getKeyValueData(
                'SELECT itype, location'
                . " FROM {$prefix}setup_images"
            );

            static::setMigrationCache('storage', 'imagesLocations', $locations);
        }

        return $locations[$type];
    }

    /**
     * @param string  $type
     * @param integer $id
     *
     * @return string or array
     */
    public function getImageURL($type, $id, $get_imgdata = false)
    {
        if (isset(static::$imageTypes[$type])) {
            $prefix = static::getTablePrefix();

            $keyField = static::$imageTypes[$type] === self::MULTIPLE_TYPE_IMAGES
                ? 'imageid'
                : 'id';

            $query = 'SELECT imageid, id, image_path, filename, image, md5, image_type, alt'
                . " FROM {$prefix}images_{$type}"
                . " WHERE {$keyField} = ?";

            $PDOStatement = static::getPreparedPDOStatement($query);
            if (
                $PDOStatement
                && $PDOStatement->execute([$id])
                && $data = $PDOStatement->fetch(\PDO::FETCH_ASSOC)
            ) {
                if (\XLite\Core\Converter::isURL($data['image_path'])) {
                    $siteDomain = preg_replace('#^(http://|https://)(www\.)?#', '', static::getSiteUrl());
                    $imageUrlWithoutProtocol = preg_replace('#^(http://|https://)(www\.)?#', '', $data['image_path']);

                    if (preg_match('#^' . $siteDomain . '(/?)images/(' . implode('|', array_keys(static::$imageTypes)) . ')#', $imageUrlWithoutProtocol)) {
                        $path = static::getSitePath();

                        if ($path) {
                            $res_path = \Includes\Utils\FileManager::makeRelativePath(
                                LC_DIR_ROOT,
                                $path . '/' . str_replace($siteDomain, '', $imageUrlWithoutProtocol)
                            );
                            return $get_imgdata ? ['path' => $res_path, 'alt' => $data['alt']] : $res_path;
                        }
                    }

                    $res_path = $data['image_path'];
                    return $get_imgdata ? ['path' => $res_path, 'alt' => $data['alt']] : $res_path;
                }

                $imageLocation = $this->getImagesLocationByType($type);
                if ($imageLocation === static::IMAGES_LOCATION_FS) {
                    $path = static::getSitePath();
                    if (static::isSiteRemote()) {
                        $res_path = static::getSiteUrl() . static::getTranslatedImagePath($data);
                        return $get_imgdata ? ['path' => $res_path, 'alt' => $data['alt']] : $res_path;
                    } elseif ($path) {
                        $imagePath = !empty($data['image_path']) ? $data['image_path'] : ('./images/' . $type . '/' . $data['filename']);
                        $imagePath = str_replace('\\', '/', $imagePath);
                        $res_path = \Includes\Utils\FileManager::makeRelativePath(
                            LC_DIR_ROOT,
                            $path . '/' . $imagePath
                        );
                        return $get_imgdata ? ['path' => $res_path, 'alt' => $data['alt']] : $res_path;
                    }

                    $res_path = static::getSiteUrl() . static::getTranslatedImagePath($data);
                    return $get_imgdata ? ['path' => $res_path, 'alt' => $data['alt']] : $res_path;
                }

                if ($imageLocation === static::IMAGES_LOCATION_DB) {
                    $path = $this->getDBImagePath($type, $data);
                    if ($path) {
                        $res_path = \Includes\Utils\FileManager::makeRelativePath(
                            LC_DIR_ROOT,
                            $path
                        );
                        return $get_imgdata ? ['path' => $res_path, 'alt' => $data['alt']] : $res_path;
                    }
                }
            }
        }

        return $get_imgdata ? [] : '';
    }

    protected function getDBImagePath($type, $data)
    {
        if (empty($data['image'])) {
            $this->getLogger('migration_skipped_db_image')->debug('', ['Processor' => get_class($this), 'Data' => $data, 'SQL' => $this->getRecordset()->getLastQuerySQL()]);
            return '';
        }

        $fileName = $data['filename'];
        $mimeType = $data['image_type'];
        $id = $data['id'];
        $imageid = $data['imageid'];

        if (empty($fileName)) {
            $fileName = \strtolower($type);
            if (!empty($id)) {
                $fileName .= "-" . $id;
            }

            if (!empty($imageid)) {
                $fileName .= "-" . $imageid;
            }

            $fileExt = $this->getDBImageExt($mimeType);
        } elseif (preg_match("/^(.+)\.([^\.]+)$/Ss", $fileName, $match)) {
            // Detect file extension
            $fileName = $match[1];
            $fileExt = $match[2];
        }

        $filePath = $this->buildUniqueDBImagePath($fileName, $fileExt, $data['md5']);

        $fd = @fopen($filePath, 'wb');
        if ($fd === false) {
            // ERROR: cannot continue
            return '';
        }

        fwrite($fd, $data['image']);
        fclose($fd);

        return $filePath;
    }

    protected function buildUniqueDBImagePath($fileName, $fileExt, $md5, $count = 0)
    {
        $fileNameTmp = $fileName;
        if ($count > 0) {
            $fileNameTmp .= '-' . $count;
        }

        $filePath = LC_DIR_TMP . $fileNameTmp . '.' . $fileExt;
        $fullFilePath = LC_DIR_ROOT . $filePath;

        if (\Includes\Utils\FileManager::isExists($fullFilePath)) {
            $hash = \Includes\Utils\FileManager::getHash($fullFilePath, false, false);

            if ($hash != $md5) {
                $filePath = $this->buildUniqueDBImagePath($fileName, $fileExt, $md5, $count + 1);
            }
        }

        return $filePath;
    }

    protected function getDBImageExt($mime_type)
    {
        static $corrected = [
            'application/x-shockwave-flash' => 'swf',
            'image/pjpeg' => 'jpg',
            'image/jpeg' => 'jpg'
        ];

        if (!is_string($mime_type) || empty($mime_type)) {
            return 'img';
        }

        if (isset($corrected[$mime_type])) {
            return $corrected[$mime_type];
        }

        if (preg_match("/^image\/(.+)$/Ss", $mime_type, $m)) {
            return $m[1];
        }

        return 'img';
    }

    protected function parseLocalContentImages($_content)
    {
        static $res = [], $webroot_dir = null;

        $md5_key = md5($_content);
        if (isset($res[$md5_key])) {
            return $res[$md5_key];
        }

        $xc4_site = array_merge(['path' => '', 'scheme' => ''], parse_url($this->getSiteUrl()));
        $xc4_url_scheme = $xc4_site['scheme'] ?: 'https';
        $xc4_url_host = $xc4_site['host'];
        $xc4_webpath = $xc4_site['path'];
        $is_remote_site = static::isSiteRemote();

        if (is_null($webroot_dir) && $xc4_sitepath = realpath($this->getSitePath())) {
            // Find Common Prefix For Xc4 Xc5 I.e. Webroot
            $len = strlen($xc4_sitepath);
            $xc5_dir_root = LC_DIR_ROOT;
            for ($i = 0; $i < $len && $xc4_sitepath[$i] == $xc5_dir_root[$i]; $i++);
            $webroot_dir = rtrim(substr($xc4_sitepath, 0, $i), '/');
        } elseif (is_null($webroot_dir)) {
            $webroot_dir = '';
        }

        $res[$md5_key] = preg_replace_callback("/(<img[^<>]+src[\s]*=[\s]*[\"']+)([^\"']+)([\"'])/i", static function ($found) use ($xc4_url_scheme, $xc4_url_host, $xc4_webpath, $is_remote_site, $webroot_dir) {
            $img_url = $found[2];
            $img_parts = array_merge(['path' => '', 'query' => ''], parse_url($img_url));

            $_method = '';
            if (!empty($img_parts['host'])) {
                if (
                    $img_parts['host'] == $xc4_url_host
                    && !empty($xc4_url_host)
                ) {
                    // the img points to the xc4 server
                    $is_same_subdir = empty($xc4_webpath) || stripos($img_parts['path'], $xc4_webpath) === 0 || $xc4_webpath == '/';
                    if (
                        $is_remote_site
                        || (
                            !empty($img_parts['path'])
                            && !empty($img_parts['query'])
                            && $is_same_subdir
                            && stripos($img_parts['path'], '.php') !== false
                        )
                    ) {
                        // TODO Dynamic URL Currently Not Supported, However, Don'T Remove The Current Empty Else Block Not To Break Logic
                        // $_method = 'loadFromURL';
                    } elseif (
                        !empty($img_parts['path'])
                        && $is_same_subdir
                    ) {
                        // try to find the img locally
                        $_method = 'loadFromPath';
                    }
                }
            } elseif (!empty($img_parts['path'])) {
                if ($is_remote_site) {
                    $_method = 'loadFromURL';
                    $img_url = $xc4_url_scheme . '://' . $xc4_url_host . '/' . $img_url; // Protocol ?
                } else {
                    // try to find the img locally
                    $_method = 'loadFromPath';
                }
            }

            $result_url = $found[2];
            if (!empty($_method)) {
                if ($_method == 'loadFromPath') {
                    $img_url = static::replaceWebDir_n_makeRelative($img_url, $xc4_url_host, $xc4_webpath, $webroot_dir);
                    $skip_file_extent_check = false;
                } else {
                    $skip_file_extent_check = true;
                }

                $_hash_n_exists = \Includes\Utils\FileManager::getHash($img_url, $skip_file_extent_check, true);
                if ($_hash_n_exists) {
                    $existing = \XLite\Core\Database::getRepo('XLite\Model\Image\Content')->findOneByHash($_hash_n_exists);

                    if ($existing && $existing->isFileExists()) {
                        $result_url = str_replace(\XLite\Core\URLManager::getShopURL(), '', $existing->getFrontURL());
                    } else {
                        $new_image = new \XLite\Model\Image\Content();

                        $copy2fs = true;
                        if ($new_image->$_method($img_url, $copy2fs)) {
                            \XLite\Core\Database::getEM()->persist($new_image);
                            $new_image->setNeedProcess(1);

                            $result_url = str_replace(\XLite\Core\URLManager::getShopURL(), '', $new_image->getFrontURL());
                        }
                    }
                }
            }

            return $found[1] . $result_url . $found[3];
        }, $_content);

        return $res[$md5_key];
    }

    private static function replaceWebDir_n_makeRelative($in_url, $in_xc4_site_url_host, $in_xc4_site_url_path, $in_webdirroot): string
    {
        $_arr = [
            'https://' . $in_xc4_site_url_host . $in_xc4_site_url_path,
            'http://' . $in_xc4_site_url_host . $in_xc4_site_url_path,
            $in_xc4_site_url_path,
        ];
        $is_found = false;
        foreach ($_arr as $replace_str) {
            if (
                !empty($replace_str)
                && stripos($in_url, $replace_str) === 0
            ) {
                $in_url = substr_replace($in_url, '', 0, strlen($replace_str));
                $is_found = true;
                break;
            };
        }

        if ($is_found || empty($in_webdirroot)) {
            $abs_pathxc4 = static::getSitePath();
        } else {
            $abs_pathxc4 = $in_webdirroot;
        }
        return  \Includes\Utils\FileManager::makeRelativePath(
            LC_DIR_ROOT,
            $abs_pathxc4 . '/' . $in_url
        );
    }

    /**
     * @var array
     */
    protected $lngData = [];

    /**
     * @param integer $id
     * @param string  $field
     *
     * @return array
     */
    protected function getLngData($id, $field)
    {
        if (!isset($this->lngData[$id])) {
            $PDOStatement = $this->getLngDataPDOStatement();
            if ($PDOStatement && $PDOStatement->execute([$id])) {
                $this->lngData[$id] = $PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
            }
        }

        $result = [];
        if (is_array($this->lngData[$id])) {
            foreach ($this->lngData[$id] as $data) {
                $data['code'] = \strtolower($data['code']);
                if ($data['code'] == 'us') {
                    $data['code'] = 'en';
                }

                $result[$data['code']] = $data[$field];
            }
        }

        return $result;
    }

    /**
     * @param integer $id
     * @param string  $field
     * @param mixed   $value
     *
     * @return mixed
     */
    protected function getI18NValues($id, $field, $value)
    {
        $result = $value;

        $data = $this->getLngData($id, $field);
        if ($data) {
            $default = $this->normalizeValueAsString($this->getDefLangValue($value));
            foreach (array_keys($value) as $code) {
                $result[$code] = !empty($data[$code]) && ($data[$code] !== $default)
                    ? $data[$code]
                    : $default;
            }
        }

        return $result;
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getLngDataPDOStatement()
    {
        return null;
    }

    /**
     * Get category by path
     *
     * @param mixed   $path     Path
     * @param boolean $useCache Use cache to get data
     *
     * @return \XLite\Model\Category|null
     */
    protected function getCategoryByPath($path, $useCache = true)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::getCategoryByPath($path, $useCache);
        }

        if (count($path) === 1 && is_object($category = reset($path))) {
            return $category;
        }

        $path = array_keys($path);

        return \XLite\Core\Database::getRepo('XLite\Model\Category')->find(end($path));
    }

    /**
     * Add category by path
     *
     * @param mixed $path Path
     *
     * @return \XLite\Model\Category
     */
    protected function addCategoryByPath($path)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::addCategoryByPath($path);
        }

        $category = $this->getCategoryByPath($path);

        if (!$category) {
            $category = new \XLite\Model\Category();
            $cacheKey = implode('/', array_keys($path));

            $ids = array_keys($path);
            $category->setCategoryId(end($ids));
            $category->setName(array_pop($path));
            $category->setParent($this->addCategoryByPath($path));
            \XLite\Core\Database::getRepo('XLite\Model\Category')->insert($category, false);

            $this->setCategoryByPathCache($cacheKey, $category);
        }

        return $category;
    }

    // }}} </editor-fold>

    // {{{ Local cache <editor-fold desc="Local cache" defaultstate="collapsed">

    /**
     * @param string $cacheName
     * @param string $key
     * @param mixed  $value
     */
    public static function setMigrationCache($cacheName, $key, $value)
    {
        $importer = static::$staticImporter;
        if ($importer) {
            $importer->setMigrationCache($cacheName, $key, $value);
        } else {
            if (!isset(static::$staticCache[$cacheName])) {
                static::$staticCache[$cacheName] = [];
            }

            static::$staticCache[$cacheName][$key] = $value;
        }
    }

    /**
     * @param string $cacheName
     * @param string $key
     *
     * @return bool
     */
    public static function hasMigrationCache($cacheName, $key)
    {
        $importer = static::$staticImporter;
        if ($importer) {
            return $importer->hasMigrationCache($cacheName, $key);
        }

        return array_key_exists($cacheName, static::$staticCache)
            && array_key_exists($key, static::$staticCache[$cacheName]);
    }

    /**
     * @param string $cacheName
     * @param string $key
     *
     * @return mixed|null
     */
    public static function getMigrationCache($cacheName, $key)
    {
        $importer = static::$staticImporter;
        if ($importer) {
            return $importer->getMigrationCache($cacheName, $key);
        }

        return static::$staticCache[$cacheName][$key] ?? null;
    }

    /**
     * Generate Cache Key To Use In Static Cache
     *
     * @param array  $inValueKey   Value key
     *
     * @return string
     */
    protected static function getCacheKeyOfModelData(array $inValueKey)
    {
        $keysList = [];

        foreach ($inValueKey as $_filter) {
            if ($_filter instanceof \XLite\Model\AEntity) {
                $keysList[] =
                    (count($inValueKey) == 1 ? get_class($_filter) : '')
                    . ($_filter->getUniqueIdentifier() ?: (property_exists($_filter, 'name') ? $_filter->name : get_class($_filter)));
            } else {
                $keysList[] = is_scalar($_filter) ? $_filter : md5(serialize($_filter));
            }
        }

        return implode('|', $keysList);
    }

    /**
     * Wrapper For GetEm->Flush Method
     */
    protected static function databaseGetEmFlush()
    {
        \XLite\Core\Database::getEM()->flush();
        static::$modelsToBeFlushed = [];
    }

    /**
     * Return model from local cache
     *
     * @param string $entityName Entity name
     * @param array  $valueKey   Value key
     * @param array  $data       Entity data
     *
     * @return \XLite\Model\AEntity
     */
    protected function getModelFromLocalCache($entityName, array $valueKey, array $data = [])
    {
        $result = null;

        if (!empty($valueKey)) {
            $result = \XLite\Core\Database::getRepo($entityName)->findOneBy($valueKey);

            if (!$result) {
                $cacheValueKey = static::getCacheKeyOfModelData($valueKey);

                if (empty($data)) {
                    // If no data provided use valueKey
                    $data = $valueKey;
                }

                if (
                    !isset($this->modelsLocalCache[$entityName])
                    || !isset($this->modelsLocalCache[$entityName][$cacheValueKey])
                ) {
                    $result = \XLite\Core\Database::getRepo($entityName)->insert($data, false);
                    if (!isset($this->modelsLocalCache[$entityName])) {
                        $this->modelsLocalCache[$entityName] = [];

                        if (!isset(static::$modelsToBeFlushed)) {
                            static::$modelsToBeFlushed = [];
                        }
                    }
                    $this->modelsLocalCache[$entityName][$cacheValueKey] = $result;

                    if (isset($data['sourceId']) && isset($data['registry'])) {
                        // Now We Know That The Entity Is Managed
                        $__cacheValueKey = static::getCacheKeyOfModelData(['registry' => $data['registry'], 'sourceId' => $data['sourceId']]);
                        static::$modelsToBeFlushed[ $__cacheValueKey ] = 1;
                    }
                } else {
                    $result = \XLite\Core\Database::getEM()->merge($this->modelsLocalCache[$entityName][$cacheValueKey]);
                }
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Files operation <editor-fold desc="File operation" defaultstate="collapsed">

    /**
     * Get file relative pPath
     *
     * @return string
     */
    protected function getFileRelativePath()
    {
        if (!$this->isMigrationProcessor()) {
            return parent::getFileRelativePath();
        }

        return $this->recordset->getPathname();
    }

    /**
     * Reset files pointer
     *
     * @return void
     */
    protected function resetPointer()
    {
        if (!$this->isMigrationProcessor()) {
            parent::resetPointer();
        }

        $this->subprocessor = null;
        $this->recordset    = null;
    }

    /**
     * Move files pointer
     *
     * @param integer $position Position
     *
     * @return boolean
     */
    protected function movePointer($position)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::movePointer($position);
        }

        $result = true;

        while ((int) $position !== 0) {
            if (is_object($this->subprocessor) && is_object($this->recordset)) {
                $recordset_count = $this->recordset->count();
                if (
                    $this->recordset->key() === 0
                    && $recordset_count < $position
                ) {
                    $this->recordset->seek($recordset_count);
                    $position -= $recordset_count;
                }
            }
            $recordset = $this->getRecordset();
            $key       = $recordset->key();
            if ((int) $position === 1) {
                $recordset->next();
            } else {
                $recordset->seek($key + $position);
            }
            if (0 < $key && $key === $recordset->key()) {
                $result = false;
                break;
            }
            $position -= $recordset->key() - $key;
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Rows collecting <editor-fold desc="Rows collecting" defaultstate="collapsed">

    /**
     * Collect raw rows
     *
     * @return array
     */
    protected function collectRawRows()
    {
        if (!$this->isMigrationProcessor()) {
            return parent::collectRawRows();
        }

        $rowRecordset = $this->getRecordset();
        $rows         = [];

        do {
            $row = $this->getNextRecord($rowRecordset);
            if ($row) {
                $rows[$rowRecordset->key()] = $row;
            }
            $eof = $rowRecordset->eof();
            if (!$eof) {
                $rowRecordset->next();
                $this->position++;
                $this->importer->getOptions()->position = $this->importer->getOptions()->position + 1;
            }
        } while (!$eof && $this->isNextSubDataset($rowRecordset, $rows));

        if (!$eof) {
            $this->rollbackRecordsetPointer($rowRecordset);
        }

        return $rows;
    }

    /**
     * Get recordset
     *
     * @return \XC\MigrationWizard\Logic\Import\Recordset
     */
    public function getRecordset()
    {
        if (
            $this->recordset === null
            || (is_object($this->recordset) && $this->recordset->eof())
            || (
                is_object($this->recordset)
                && $this->recordset->key() == $this->countCache[$this->recordset->getPathname()]
            )
        ) {
            if (($subprocessors = static::getSubProcessorsWithData()) && !empty($subprocessors)) {
                $spcname = null;
                $found   = false;

                foreach ($subprocessors as $sp) {
                    if ($this->recordset === null || $found) {
                        $spcname = $sp;
                        break;
                    }
                    if ($this->recordset !== null && $sp == $this->recordset->getPathname()) {
                        $found = true;
                    }
                }

                if ($spcname) {
                    $this->subprocessor = new $spcname($this->importer);
                    $this->recordset    = $this->defineRecordset($this->subprocessor);

                    if ($this->recordset) {
                        $this->subprocessor->getColumns();
                    }
                } elseif ($this->recordset === null) {
                    $this->subprocessor = false;
                    $this->recordset    = false;
                }
            } else {
                $this->recordset = $this->defineRecordset($this);

                if ($this->recordset) {
                    $this->getColumns();
                }
            }
        }

        return $this->recordset;
    }

    /**
     * Get next row
     *
     * @param \XC\MigrationWizard\Logic\Import\Recordset $recordset Recordset
     *
     * @return array
     */
    protected function getNextRecord(\XC\MigrationWizard\Logic\Import\Recordset $recordset)
    {
        $record = null;

        do {
            // Reinitialize recordset
            $recordset->seek($recordset->key());

            $record = $recordset->current();

            if ($this->isEmptyRow($record)) {
                $record = null;
                $recordset->next();
                $this->position++;
                $this->importer->getOptions()->position = $this->importer->getOptions()->position + 1;
            }
        } while (!$recordset->eof() && !$record);

        return $record;
    }

    /**
     * Check - next row is subrow or not
     *
     * @param \XC\MigrationWizard\Logic\Import\Recordset $recorset Recordset
     * @param array                                                   $rows     Rows list
     *
     * @return boolean
     */
    protected function isNextSubDataset(\XC\MigrationWizard\Logic\Import\Recordset $recorset, array $rows)
    {
        $result = !$recorset->eof() && !$this->isColumnHeadersEmpty();

        if ($result) {
            $record = $this->getNextRecord($recorset);
            $result = (bool) $record;
            $empty  = true;

            if ($result) {
                $first = current($rows);
                foreach ($this->getKeyColumns() as $column) {
                    $empty = $empty && !(bool) $this->getColumnValue($column, $first);
                    if ($this->getColumnValue($column, $first) !== $this->getColumnValue($column, $record)) {
                        $result = false;
                        break;
                    }
                }

                $result = $empty ? false : $result;
            }
        }

        return $result;
    }

    /**
     * Rollback file pointer
     *
     * @param \XC\MigrationWizard\Logic\Import\Recordset $recordset Recordset
     *
     * @return void
     */
    protected function rollbackRecordsetPointer(\XC\MigrationWizard\Logic\Import\Recordset $recordset)
    {
        $recordset->seek($recordset->key() - 1);
        $this->position--;
        $this->importer->getOptions()->position = $this->importer->getOptions()->position - 1;
    }

    // }}} </editor-fold>

    // {{{ Row processing <editor-fold desc="Rows processing" defaultstate="collapsed">

    /**
     * Process current row
     *
     * @param string $mode Mode
     *
     * @return boolean
     */
    public function processCurrentRow($mode)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::processCurrentRow($mode);
        }

        $this->mode = $mode;

        $result = false;

        if ($this->isRowProcessingAllowed()) {
            if ($this->recordset->key() === 0) {
                static::databaseGetEmFlush();
                $this->initialize();
            }

            $rawRows             = $this->collectRawRows();
            $this->rowStartIndex = key($rawRows);
            if ($this->isHeaderRow($rawRows)) {
                $result                                               = $this->processHeaderRow($rawRows);
            } else {
                $data = $this->assembleColumnsData($rawRows);

                if (!empty($data)) {
                    // Can Be Used In Many Normalize*Value Functions In CheckEntityChecksum->PrepareRawDataForChecksum-> Function
                    $this->currentRowData = $data;
                }

                if (static::isUseEntityCache() && $this->checkEntityChecksum($data)) {
                    return true;
                }

                if (!empty($data)) {
                    if ($this->isVerification()) {
                        $result = $this->verifyData($data);
                    } else {
                        $result = $this->importData($data);

                        if ($result && !$this->shouldSkipChecksumReplace($data)) {
                            $this->replaceEntityChecksum($data);
                        } elseif (!$result) {
                            $this->getLogger('migration_skipped_data')->debug('', ['Processor' => get_class($this), 'Data' => $data, 'SQL' => $this->getRecordset()->getLastQuerySQL()]);
                        }
                    }
                } else {
                    $result = true;
                }
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Seekable iterator <editor-fold desc="Seekable iterator" defaultstate="collapsed">

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        if (!$this->isMigrationProcessor()) {
            return parent::valid();
        }

        $importer = $this->importer;

        return $this->getRecordset()
            && !$this->getRecordset()->eof()
            && (
                $this->isVerification()
                || !$importer::hasErrors()
            );
    }

    // }}} </editor-fold>

    // Update model <editor-fold desc="Update model" defaultstate="collapsed">

    /**
     * Update model
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModel(\XLite\Model\AEntity $model, array $data)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::updateModel($model, $data);
        }

        $result = parent::updateModel($model, $data);

        if ($result) {
            $this->currentlyProcessingModel = $model;
        }

        return $result;
    }

    /**
     * Update model fields
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModelFields(\XLite\Model\AEntity $model, array $data)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::updateModelFields($model, $data);
        }

        $this->getRepository()->update($model, $this->assembleModelFields($data), false);

        $context = $this->subprocessor ?: $this;

        foreach ($this->getColumns() as $name => $column) {
            if (
                isset($data[$name])
                && $this->isModelPlainProperty($data[$name], $column)
            ) {
                $importer = $this->subprocessor
                    ? $this->subprocessor->prepareColumnHandler($column, static::COLUMN_IMPORTER, 'import{name}Column')
                    : $column[static::COLUMN_IMPORTER];

                if (method_exists($context, $importer)) {
                    call_user_func([$context, $importer], $model, $data[$name], $column, $this->currentRowData);
                }
            }
        }

        return true;
    }

    /**
     * Update model associations
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $data  Data
     *
     * @return boolean
     */
    protected function updateModelAssociations(\XLite\Model\AEntity $model, array $data)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::updateModelAssociations($model, $data);
        }

        $result = true;

        $context = $this->subprocessor ?: $this;

        foreach ($this->getColumns() as $name => $column) {
            if (isset($data[$name]) && !$this->isModelPlainProperty($data[$name], $column)) {
                $subresult = true;
                if (!empty($column[static::COLUMN_IMPORTER])) {
                    $subresult = call_user_func(
                        [$context, $column[static::COLUMN_IMPORTER]],
                        $model,
                        $data[$name],
                        $column
                    );

                    if ($subresult === false) {
                        $result = false;
                    }
                } elseif ($this->isColumnMultilingual($column)) {
                    $value = $this->normalizeModelPlainProperty($data[$name], $column);

                    if (is_object($value)) {
                        // This Else Block Currently Not Supported For ParseLocalContentImages As $Value Here Is Not Updated
                        $this->getRepository()->update(
                            $model,
                            [$this->getModelPropertyName($column) => $this->updateModelTranslations($value, $data[$name])],
                            false
                        );
                    } else {
                        if (!empty($column[static::COLUMN_PARSE_IMAGES]) && $column[static::COLUMN_PARSE_IMAGES] === 'parse_after_normalization') {
                            $value = array_map(function ($content) {
                                return $this->parseLocalContentImages($content);
                            }, $value);
                        }

                        parent::updateModelTranslations($model, $value, $this->getModelPropertyName($column));
                    }
                }
            }
        } // Foreach ($This->GetColumns() As $Name => $Column)

        return $result;
    }

    /**
     * Update model translations
     *
     * @param \XLite\Model\AEntity $model Model
     * @param array                $value Value
     * @param string               $name  Name OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    protected function updateModelTranslations(\XLite\Model\AEntity $model, array $value, $name = 'name')
    {
        if (!$this->isMigrationProcessor()) {
            return parent::updateModelTranslations($model, $value, $name);
        }

        $ucfName    = ucfirst($name);
        $methodName = "normalize{$ucfName}Value";

        if (method_exists($this, $methodName)) {
            $value = $this->$methodName($value);
        }

        return parent::updateModelTranslations($model, $value, $name);
    }

    // </editor-fold>

    // {{{ Registry <editor-fold desc="Registry" defaultstate="collapsed">

    /**
     * Register model in registry
     *
     * @param string $className
     * @param mixed  $sourceId
     * @param string $resultId
     *
     * @throws \Exception
     */
    protected function registerModelInRegistry($className, $sourceId, $resultId)
    {
        $registry = $this->getModelFromLocalCache(
            'XC\MigrationWizard\Model\MigrationRegistry',
            ['name' => $className]
        );

        if ($registry && !$registry->getRegistryId()) {
            // The Registry In Memory, So Flush It To Work The Search Below Work Properly
            try {
                static::databaseGetEmFlush();
            } catch (\Doctrine\DBAL\Exception\ConstraintViolationException $e) {
                static::getStaticLogger('migration_errors')->error($e->getMessage(),
                    ['processor' => get_class($this), 'method' => __METHOD__, 'getSQL()' => $e->getQuery()->getSQL(), 'getParams()' => $e->getQuery()->getParams(), '$className' => $className]
                );
                throw $e;
            }
        }

        if (is_array($sourceId)) {
            $sourceId = $this->getDefLangValue($sourceId) ?: reset($sourceId);
        }

        $entry = $this->getModelFromLocalCache(
            'XC\MigrationWizard\Model\MigrationRegistryEntry',
            ['registry' => $registry, 'sourceId' => $sourceId],
            ['registry' => $registry, 'sourceId' => $sourceId, 'resultId' => $resultId]
        );

        $result = ($registry && $entry);
        if (!$result) {
            $data = [
                'className' => $className,
                'sourceId'  => $sourceId,
                'resultId'  => $resultId,
            ];

            $this->getLogger('migration_errors')->debug('migration_errors', [
                'registerModel' => 'Failed to register model',
                'Data'          => $data,
                'SQL'           => $this->getRecordset()->getLastQuerySQL(),
            ]);

            \XC\MigrationWizard\Logic\Migration\Wizard::registerTransferDataError();
        }

        return $result
            ? static::REGISTRY_REGISTER_SUCCESS
            : static::REGISTRY_REGISTER_FAILED;
    }

    /**
     * Get model from registry
     *
     * @param array $data
     *
     * @return \XLite\Model\AEntity
     */
    protected function getModelFromRegistry(array $data)
    {
        $result = null;

        if ($this->isModelRegistrationRequired()) {
            $entryInfo = static::defineRegistryEntry();
            $sourceId  = $data[$entryInfo[static::REGISTRY_SOURCE]];

            $repository = !empty($entryInfo[static::REGISTRY_ENTITY])
                ? \XLite\Core\Database::getRepo($entryInfo[static::REGISTRY_ENTITY])
                : $this->getRepository();

            $entry = static::getEntryFromRegistryByClassAndSourceId($repository->getClassName(), $sourceId);

            if ($entry) {
                $result = $repository->find($entry->getResultId());
            }
        }

        return $result;
    }

    /**
     * Save model in registry
     *
     * @param array $data
     * @param bool $_force_flush
     *
     * @return string
     */
    protected function saveModelInRegistry(array $data, bool $_force_flush = false)
    {
        $result = static::REGISTRY_REGISTER_NOT_REQUIRED;

        if (
            $this->isModelRegistrationRequired()
            && $this->currentlyProcessingModel
        ) {
            $entryInfo = static::defineRegistryEntry();
            $sourceId  = $data[$entryInfo[static::REGISTRY_SOURCE]];

            if ($_force_flush && !$this->currentlyProcessingModel->{$entryInfo[static::REGISTRY_RESULT]}) {
                static::databaseGetEmFlush();
            }

            $resultId  = $this->currentlyProcessingModel->{$entryInfo[static::REGISTRY_RESULT]};

            $className = !empty($entryInfo[static::REGISTRY_ENTITY])
                ? $entryInfo[static::REGISTRY_ENTITY]
                : $this->getRepository()->getClassName();

            $result = $this->registerModelInRegistry($className, $sourceId, $resultId);
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize model plain property value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return mixed
     */
    protected function normalizeModelPlainProperty($value, array $column)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::normalizeModelPlainProperty($value, $column);
        }

        $context = $this->subprocessor ?: $this;

        $normalizer = $column[static::COLUMN_NORMALIZATOR];
        if (!$normalizer && $this->subprocessor) {
            $normalizer = $this->subprocessor->prepareColumnHandler($column, static::COLUMN_NORMALIZATOR, 'normalize{name}Value');
        }

        return $normalizer && method_exists($context, $normalizer)
            ? call_user_func([$context, $normalizer], $value, $this->currentRowData)
            : $value;
    }

    /**
     * Normalize value as membership
     *
     * @param mixed $value Value
     *
     * @return \XLite\Model\Membership
     */
    protected function normalizeValueAsMembership($value)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::normalizeValueAsMembership($value);
        }

        $membership = parent::normalizeValueAsMembership($value);

        if ($membership) {
            $className = 'XLite\Model\Membership';

            $sourceId = $membership->getName();
            $resultId = $membership->getMembershipId();

            if ($resultId) {
                $this->registerModelInRegistry($className, $sourceId, $resultId);
            }
        }

        return $membership;
    }

    // }}} </editor-fold>

    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function createModel(array $data)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::createModel($data);
        }

        if ($this->subprocessor) {
            return call_user_func([$this->subprocessor, __FUNCTION__], $data);
        }

        $classesWithFixedId = [
            'XLite\Model\Product'                    => ['productId', 'product_id'],
            'XLite\Model\Category'                   => ['categoryId', 'category_id'],
            'XLite\Model\Zone'                       => ['zone_id', 'zone_id'],
            'CDev\SimpleCMS\Model\Page' => ['id', 'id'],
        ];

        $className = $this->getRepository()->getClassName();
        if (isset($classesWithFixedId[$className])) {
            [$idKey, $idName] = $classesWithFixedId[$className];

            if (isset($data[$idKey])) {
                return $this->getRepository()->insert([$idName => $data[$idKey]], false);
            }
        }

        return parent::createModel($data);
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::detectModel($data);
        }

        try {
            $result = $this->getModelFromRegistry($data);

            if (!$result) {
                $result = parent::detectModel($data);
                if (!$result) {
                    // May Be The Model In Memory? Try To Search
                    $_conditions = $this->assembleModelConditions($data);

                    $_model_cache_key = static::getCacheKeyOfModelData($_conditions);
                    if (isset(static::$modelsToBeFlushed[$_model_cache_key])) {
                        static::databaseGetEmFlush();
                        $result = parent::detectModel($data);
                    }
                }
            }
        } catch (\Exception $ex) {
            $this->getLogger('migration_errors')->debug('', [
                'detectModel' => $ex->getMessage(),
                'Data' => $data,
                'SQL' => $this->getRecordset()->getLastQuerySQL(),
                'trace' => debug_backtrace()
            ]);

            \XC\MigrationWizard\Logic\Migration\Wizard::registerTransferDataError();
        }

        return $result;
    }

    /**
     * Import data
     *
     * @param array $data Row set Data
     *
     * @return boolean
     */
    protected function importData(array $data)
    {
        if (!$this->isMigrationProcessor()) {
            return parent::importData($data);
        }

        // We need to save variantID
        if ($this instanceof \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\ProductVariantsValues) {
            $data[static::VARIANT_PREFIX . 'ID'] = $this->variantIdsList;
        }

        try {
            // Detected addition
            $add_count_before = (int) $this->getMetaData('addCount');
            $result = parent::importData($data);

            if ($result) {
                $new_is_added = ((int) $this->getMetaData('addCount')) > $add_count_before;

                $res_registration = $this->saveModelInRegistry($data, $new_is_added);

                if ($new_is_added && $res_registration === static::REGISTRY_REGISTER_NOT_REQUIRED) {
                    $conditions = $this->assembleModelConditions($data);
                    $model_cache_key = static::getCacheKeyOfModelData($conditions);
                    static::$modelsToBeFlushed[ $model_cache_key ] = 1;
                }
            }
        } catch (\Exception $ex) {
            $result = false;
            $this->getLogger('migration_errors')->debug('', [
                'ImportData' => $ex->getMessage(),
                'Data' => $data,
                'SQL' => $this->getRecordset()->getLastQuerySQL(),
                'trace' => debug_backtrace()
            ]);

            if (!\XLite\Core\Database::getEM()->isOpen()) {
                \XLite\Core\Database::getInstance()->startEntityManager();
                static::updateMetadata();
            }

            \XC\MigrationWizard\Logic\Migration\Wizard::registerTransferDataError();
        }

        return $result;
    }

    /**
     * Import 'xc4EntityId' value
     *
     * @param \XLite\Model\AEntity $model  Model
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importXc4EntityIdColumn($model, $value, array $column)
    {
    }

    /**
     * Import 'CleanURLId' value
     *
     * @param \XLite\Model\AEntity $model  Model
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importCleanURLIdColumn($model, $value, array $column)
    {
    }

    /**
     * Import 'CleanURLId' value
     *
     * @param \XLite\Model\AEntity $model  Model
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importCleanURLTypeColumn($model, $value, array $column)
    {
    }

    /**
     * Import Origin Xc4 Clean Url + Clean History
     */
    public function importOriginCleanURL_n_History($model, $value, $all_data)
    {
        $entity_id = empty($all_data['cleanURLId']) ? $all_data['xc4EntityId'] : $all_data['cleanURLId'];
        $entity_type = empty($all_data['cleanURLType']) ? '' : $all_data['cleanURLType'];
        $old_urls = [];

        // Find Urls From History
        if (!empty($entity_id) && !empty($entity_type)) {
            $prefix = static::getTablePrefix();

            $PDOStatement = static::getPreparedPDOStatement(
                'SELECT '
                . 'clean_url'
                . " FROM {$prefix}clean_urls_history"
                . ' WHERE resource_id = ? AND resource_type = ?'
            );

            if ($PDOStatement && $PDOStatement->execute([$entity_id, $entity_type])) {
                $old_urls = $PDOStatement->fetchAll(\PDO::FETCH_COLUMN) ?: [];
                foreach ($old_urls as $k => $_url) {
                    $old_urls[$k] = $this->getCleanUrl(\strtolower($entity_type), $_url);
                }
                $old_urls = array_combine(array_map('\strtolower', $old_urls), $old_urls);
            }
        }

        // Delete History URLs That Already Used
        if (!empty($old_urls)) {
            $_in  = str_repeat('?,', count($old_urls) - 1) . '?';
            $PDOStatement = static::getPreparedPDOStatement("SELECT clean_url FROM {$prefix}clean_urls WHERE resource_type = ? AND clean_url IN ($_in)");

            if ($PDOStatement && $PDOStatement->execute(array_values(array_merge([$entity_type], $old_urls)))) {
                $actual_urls = $PDOStatement->fetchAll(\PDO::FETCH_COLUMN) ?: [];
                foreach ($actual_urls as $_exist_url) {
                    if (isset($old_urls[\strtolower($_exist_url)])) {
                        unset($old_urls[\strtolower($_exist_url)]);
                    }
                }
            }
        }

        if (!empty($value) || !empty($old_urls)) {
            $old_urls[\strtolower($value)] = $value;
            foreach ($model->getCleanURLs() as $cleanURL) {
                $exist_url = \strtolower($cleanURL->getCleanURL() ?: '');
                if (isset($old_urls[$exist_url])) {
                    unset($old_urls[$exist_url]);
                }

                if (empty($old_urls)) {
                    break;
                }
            }

            if (!empty($old_urls)) {
                $_cleanUrls = [];

                // Form An Array to Add at the top
                foreach ($old_urls as $value) {
                    $cleanURLObject = new \XLite\Model\CleanURL();
                    $cleanURLObject->setEntity($model);
                    $cleanURLObject->setCleanURL($value);

                    $_cleanUrls[] = $cleanURLObject;
                }

                foreach ($model->getCleanURLs() as $cleanURL) {
                    $cleanURLObject = new \XLite\Model\CleanURL();
                    $cleanURLObject->setEntity($model);
                    $cleanURLObject->setCleanURL($cleanURL->getCleanURL());

                    $_cleanUrls[] = $cleanURLObject;

                    \XLite\Core\Database::getEM()->remove($cleanURL);
                }

                $model->setCleanURLs(new \Doctrine\Common\Collections\ArrayCollection($_cleanUrls));
            }
        }
    }

    /**
     * Update entities metadata
     */
    public static function updateMetadata()
    {
    }

    // }}} </editor-fold>

    private static \Doctrine\Common\Cache\CacheProvider $cacheDriver;

    public static function clearAllCache()
    {
        if (static::getCacheDriver()) {
            static::getCacheDriver()->deleteAll();
        }
    }

    protected static function getCacheDriver() : \Doctrine\Common\Cache\CacheProvider
    {
        if (!isset(static::$cacheDriver)) {
            try {
                static::$cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
                static::$cacheDriver->setNamespace('migration_wizard_cache');
            } catch (\Exception $e) {
                static::getStaticLogger('migration_errors')->critical($e->getMessage(), ['trace' => $e->getTrace()]);
            }
        }

        return static::$cacheDriver;
    }

    protected static function saveInFileCache($key, $data)
    {
        if (static::getCacheDriver()) {
            if (!is_scalar($key)) {
                $key = md5(serialize($key));
            }

            static::getCacheDriver()->save($key, $data, static::CACHE_TTL);
        }
    }

    protected static function getFromFileCache($key)
    {
        $data = false;

        if (static::getCacheDriver()) {
            if (!is_scalar($key)) {
                $key = md5(serialize($key));
            }

            $data = static::getCacheDriver()->fetch($key);
        }

        return $data;
    }

    /**
     * Add warning
     *
     * @param string  $code      Message code
     * @param array   $arguments Message arguments OPTIONAL
     * @param integer $rowOffset Row offset OPTIONAL
     * @param array   $column    Column info OPTIONAL
     * @param mixed   $value     Value OPTINAL
     *
     * @return boolean
     */
    protected function addWarning(
        $code,
        array $arguments = [],
        $rowOffset = 0,
        array $column = [],
        $value = null
    ) {
        if (!$this->isMigrationProcessor()) {
            return parent::addWarning($code, $arguments, $rowOffset, $column, $value);
        }

        $this->importer->getOptions()->warningsCount += 1;

        return true;
    }

    /**
     * Add error
     *
     * @param string  $code      Message code
     * @param array   $arguments Message arguments OPTIONAL
     * @param integer $rowOffset Row offset OPTIONAL
     * @param array   $column    Column info OPTIONAL
     * @param mixed   $value     Value OPTINAL
     *
     * @return boolean
     */
    protected function addError(
        $code,
        array $arguments = [],
        $rowOffset = 0,
        array $column = [],
        $value = null
    ) {
        if (!$this->isMigrationProcessor()) {
            return parent::addError($code, $arguments, $rowOffset, $column, $value);
        }

        $this->importer->getOptions()->errorsCount += 1;

        return true;
    }
}
