<?php
// phpcs:ignoreFile
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import;

use XLite\InjectLoggerTrait;

/**
 * Recordset
 */
class Recordset implements \SeekableIterator, \Countable
{
    use InjectLoggerTrait;

    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const DATA_SOURCE = 'Source';
    public const DATA_FILTER = 'Filter';
    public const DATA_SORTER = 'Sorter';
    public const DATA_GROUPER = 'Grouper';
    public const DATA_COUNT_FIELDS = 'CountFields';

    public const ID_GENERATOR = 'Generator';

    public const GENERATOR_PLACEHOLDER = '/*#GENERATOR-PLACEHOLDER#*/';
    public const GENERATOR_SOURCE      = 'GeneratorSource';
    public const GENERATOR_FIELDS      = 'GeneratorFields';
    public const GENERATOR_ALIAS       = 'GeneratorAlias';
    public const GENERATOR_ORDERBY     = 'GeneratorOrderBy';

    public const NO_DATA_SOURCE = 'Self defined record';

    public const FOR_DISPLAY_COUNT = 'for_display_count';

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    protected $_count = [];
    protected $position;

    protected $_dataset = [
        self::DATA_SOURCE => false,
        self::DATA_FILTER => false,
    ];

    protected $fromStatement = [];

    protected $fieldSet = '*';
    protected $fields;

    /**
     * @var bool|\PDO
     */
    protected $connection;

    protected $_generator = null;

    protected $_processor = null;
    protected $lastQuerySQL;

    protected $recordsCache = [];
    protected $limit        = 0;

    // }}} </editor-fold>

    // {{{ Constructor <editor-fold desc="Constructor" defaultstate="collapsed">

    /**
     * Constructor
     *
     * @param             $processorClass
     * @param string      $dataSet
     * @param string      $fieldSet
     * @param bool|string $dataFilter
     * @param array       $dataSorter
     * @param bool|string $generator
     * @param string      $count_fields
     * @param array       $dataGrouper
     *
     * @throws \Exception
     */
    public function __construct($processorClass, $dataSet, $fieldSet = '*', $dataFilter = false, array $dataSorter = [], $generator = false, $count_fields = '', array $dataGrouper = [])
    {
        $this->_processor = $processorClass;

        $this->fieldSet = rtrim($fieldSet, ' ,');

        $this->_dataset[self::DATA_SOURCE]  = $dataSet;
        $this->_dataset[self::DATA_FILTER]  = $dataFilter;
        $this->_dataset[self::DATA_SORTER]  = $dataSorter;
        $this->_dataset[self::ID_GENERATOR] = $generator;
        $this->_dataset[self::DATA_COUNT_FIELDS] = $count_fields;
        $this->_dataset[self::DATA_GROUPER]  = $dataGrouper;

        $this->limit = \XLite\Core\EventListener\Import::CHUNK_LENGTH; // cache buffer size

        if (defined('MIGRATION_WIZARD_CHUNK')) {
            $this->limit = MIGRATION_WIZARD_CHUNK;
        }

        $this->connection = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance()
            ->getStep('Connect')
            ->getConnection();

        if (empty($this->connection)) {
            throw new \Exception('No valid SQL server connection provided');
        }

        if ($this->count() > 0) {
            $this->position = 0;
            $this->getFields();
        }
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get fields list in data set
     *
     * @return array
     * @throws \Exception
     */
    protected function getFields()
    {
        if ($this->fields === null) {
            $fromQuery = $this->getFromQuery();

            if ($fromQuery !== null) {
                $this->fields       = [];
                $this->lastQuerySQL = "SELECT {$this->fieldSet} {$fromQuery} LIMIT 1";

                $query = $this->connection->query($this->lastQuerySQL);

                if ($query) {
                    if ($record = $query->fetch(\PDO::FETCH_ASSOC)) {
                        $this->fields = array_keys($record);
                    }
                } else {
                    throw new \Exception('Invalid SQL: ' . $this->lastQuerySQL . ' Error: ' . implode(' ', $this->connection->errorInfo()));
                }
            }
        }

        return $this->fields;
    }

    /**
     * Get record from dataset at current position
     *
     * @return array
     * @throws \Exception
     */
    protected function getRecord()
    {
        if ($this->position === 0) {
            return $this->getFields();
        }

        $result = [];

        if (!isset($this->recordsCache[$this->position])) {
            $fromQuery = $this->getFromQuery();
            if ($fromQuery !== null) {
                $limitStart = $this->position - 1; // fields row offset
                $limitCount = $this->limit;

                $limitConditions = "LIMIT {$limitStart}, {$limitCount}";

                $generator = $this->getGeneratorData();

                if ($generator !== null) {
                    $this->lastQuerySQL = "SELECT {$generator[self::GENERATOR_FIELDS]} {$generator[self::GENERATOR_SOURCE]} {$generator[self::GENERATOR_ORDERBY]} {$limitConditions}";

                    try {
                        $query = $this->connection->query($this->lastQuerySQL);
                    } catch (\PDOException $e) {
                        static::getStaticLogger('migration_errors')->error($e->getMessage(),
                            ['processor' => get_class($this->_processor), 'lastQuerySQL' => $this->lastQuerySQL, 'method' => __METHOD__]);
                        throw $e;
                    }

                    if ($query && ($filters = $query->fetchAll(\PDO::FETCH_ASSOC)) && $filters !== false) {
                        $list = [];

                        foreach ($filters as $fields) {
                            foreach ($fields as $field => $value) {
                                $sqlValue = $this->connection->quote($value);
                                if (!empty($list[$field])) {
                                    $list[$field] .= ", {$sqlValue}";
                                } else {
                                    $list[$field] = "$field IN ({$sqlValue}";
                                }
                            }
                        }

                        $mapedList = array_map(static function ($n) {
                            return $n . ')';
                        }, $list);

                        if (!empty($mapedList)) {
                            $filter = ' AND ' . implode(' AND ', $mapedList);
                            if (!empty($filter)) {
                                $fromQuery       = str_replace(self::GENERATOR_PLACEHOLDER, $filter, $fromQuery);
                                $limitConditions = ''; // clear default limits
                            }
                        }
                    }
                }

                $this->lastQuerySQL = "SELECT {$this->fieldSet} {$fromQuery} {$limitConditions}";

                $query = $this->connection->query($this->lastQuerySQL);

                $index = $this->position;

                while ($query && ($record = $query->fetch(\PDO::FETCH_NUM)) && $record !== false) {
                    $this->recordsCache[$index] = $record;

                    $index++;
                }

                $result = $this->recordsCache[$this->position];
            }
        } else {
            $result = $this->recordsCache[$this->position];
        }

        return $result;
    }

    /**
     * Get from SQL query
     *
     * @return string|null
     */
    protected function getFromQuery($mode = '')
    {
        if (!isset($this->fromStatement[$mode])) {
            // In case of NO_DATA_SOURCE use empty FROM to allow self SELECT
            if ($this->_dataset[self::DATA_SOURCE] === self::NO_DATA_SOURCE) {
                $this->fromStatement[$mode] = '';
            } else {
                if (!empty($this->_dataset[self::DATA_SOURCE])) {
                    $this->fromStatement[$mode] = ' FROM ' . $this->_dataset[self::DATA_SOURCE];
                }

                if (!empty($this->_dataset[self::DATA_FILTER])) {
                    $this->fromStatement[$mode] .= ' WHERE ' . $this->_dataset[self::DATA_FILTER];
                }

                if ($mode != static::FOR_DISPLAY_COUNT) {
                    // Most usable mode
                    if (!empty($this->_dataset[self::DATA_GROUPER])) {
                        $this->fromStatement[$mode] .= ' GROUP BY ' . implode(', ', $this->_dataset[self::DATA_GROUPER]);
                    }

                    if (!empty($this->_dataset[self::DATA_SORTER])) {
                        $this->fromStatement[$mode] .= ' ORDER BY ' . implode(', ', $this->_dataset[self::DATA_SORTER]);
                    }
                }
            }
        }

        return $this->fromStatement[$mode] ?? null;
    }

    /**
     * Get ID generator data
     *
     * @return array
     */
    protected function getGeneratorData()
    {
        if ($this->_generator === null) {
            if (
                !empty($this->_dataset[self::ID_GENERATOR]['table'])
                && ($table = $this->_dataset[self::ID_GENERATOR]['table'])
            ) {
                $alias = '';

                if (!empty($this->_dataset[self::ID_GENERATOR]['alias'])) {
                    $alias = $this->_dataset[self::ID_GENERATOR]['alias'];
                }

                $orderBy = '';
                if (
                    !empty($this->_dataset[self::ID_GENERATOR]['order'])
                    && empty($this->_dataset[self::DATA_SORTER])
                ) {
                    $orderBy = ' ORDER BY ' . implode(", ", $this->_dataset[self::ID_GENERATOR]['order']);
                }

                $this->lastQuerySQL = 'SHOW COLUMNS FROM ' . $table . " WHERE `Key` = 'PRI'";
                $keysQuery          = $this->connection->query($this->lastQuerySQL);

                if (
                    $keysQuery
                    && ($primaryKeys = $keysQuery->fetchAll(\PDO::FETCH_ASSOC))
                    && $primaryKeys !== false
                ) {
                    $columnsList = [];
                    $keysList    = [];
                    $fieldsList  = [];

                    foreach ($primaryKeys as $primaryKey) {
                        $aliasedFieldName = !empty($alias)
                            ? $alias . '.' . $primaryKey['Field']
                            : $primaryKey['Field'];

                        $fieldsList[]  = $aliasedFieldName;
                        $columnsList[] = "`{$aliasedFieldName}` {$primaryKey['Type']}";
                        $keysList[]    = "`{$aliasedFieldName}`";
                    }

                    if (!empty($fieldsList)) {
                        // Build columns string
                        $tableColumns = implode(', ', $columnsList);
                        // Build keys string
                        $tableKeys = implode(', ', $keysList);

                        // Create ID generator table
                        $this->lastQuerySQL = "DROP TABLE IF EXISTS `{$table}_xc5mw_id_generator`;"
                            . "CREATE TEMPORARY TABLE `{$table}_xc5mw_id_generator`("
                            . "{$tableColumns},"
                            . "PRIMARY KEY ({$tableKeys})"
                            . ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

                        $prepared = $this->connection->prepare($this->lastQuerySQL);
                        $gtc      = $prepared->execute();

                        if ($gtc !== false) {
                            // Build fields string
                            $selectFields = implode(', ', $fieldsList);

                            // Insert ID data
                            $this->lastQuerySQL = "INSERT INTO {$table}_xc5mw_id_generator"
                                . " SELECT {$selectFields}"
                                . " {$this->getFromQuery()}{$orderBy}";

                            $prepared = $this->connection->prepare($this->lastQuerySQL);

                            try {
                                $gti = $prepared->execute();
                            } catch (\PDOException $e) {
                                if (
                                    $e->getCode() === '23000'
                                    || stripos($e->getMessage(), 'Integrity constraint violation: 1062 Duplicate entry') !== false
                                ) {
                                    // legitimate error just do nothing. Data is already present
                                    $gti = false; // we have to set false here to meet if ($gti !== false) condition below . INSERT IGNORE doesn't suit
                                } else {
                                    static::getStaticLogger('migration_errors')->error($e->getMessage(),
                                        ['processor' => get_class($this->_processor), 'lastQuerySQL' => $this->lastQuerySQL, 'method' => __METHOD__, 'result of defineDatagrouper' => call_user_func_array([$this->_processor, 'defineDatagrouper'], [])]);
                                    throw $e;
                                }
                            }

                            if ($gti !== false) {
                                // Build generator fields
                                $generatorFields = '`' . implode('`,`', $fieldsList) . '`';
                                // Define generator data
                                $this->_generator = [
                                    self::GENERATOR_SOURCE  => " FROM {$table}_xc5mw_id_generator",
                                    self::GENERATOR_FIELDS  => $generatorFields,
                                    self::GENERATOR_ORDERBY => " ORDER BY $generatorFields",
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $this->_generator;
    }

    /**
     * Get last executed SQL query
     *
     * @return string
     */
    public function getLastQuerySQL()
    {
        return $this->lastQuerySQL;
    }

    /**
     * Return fields count number
     *
     * @return integer
     */
    public function getFieldsCount()
    {
        $fields = $this->getFields();

        return is_array($fields) ? count($fields) : 0;
    }

    // }}} </editor-fold>

    // {{{ File <editor-fold desc="File" defaultstate="collapsed">

    /**
     * Return TRUE in case EOF
     *
     * @return boolean
     */
    public function eof()
    {
        return ($this->position > $this->count() - 1)
            || ($this->count() - 1 < 0);
    }

    /**
     * Return pseudo pathname
     *
     * @return string
     */
    public function getPathname()
    {
        return get_class($this->_processor);
    }

    /**
     * Return pseudo filename
     *
     * @return string
     */
    public function getFilename()
    {
        $parts = explode('\\', $this->getPathname());

        return strtolower(strtolower(array_pop($parts)));
    }

    // }}} </editor-fold>

    // {{{ Seekable <editor-fold desc="Seekable" defaultstate="collapsed">

    /**
     * Get current key
     *
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Move to next record
     *
     * @return void
     */
    public function next()
    {
        $this->seek($this->key() + 1);
    }

    /**
     * Get current record
     *
     * @return array
     */
    public function current()
    {
        return $this->getRecord();
    }

    /**
     * Rewind to first record
     *
     * @return void
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Validate iteration
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->position <= $this->count();
    }

    /**
     * Move to position
     *
     * @param integer $position
     */
    public function seek($position)
    {
        if ($this->position != $position && $position <= $this->count()) {
            $this->position = $position;
        }
    }

    // }}} </editor-fold>

    // {{{ Countable <editor-fold desc="Countable" defaultstate="collapsed">

    /**
     * Get records count in dataset
     *
     * @return integer
     */
    public function count($count_mode = '')
    {
        if (
            !empty($this->_dataset[self::DATA_GROUPER])
            && !empty($this->_dataset[self::DATA_COUNT_FIELDS])
        ) {
            // Display Mode And The Run Mode Are The Same. Processor Has Both Distinct/Groupby Set
            $count_mode = static::FOR_DISPLAY_COUNT;
        }

        if (!isset($this->_count[$count_mode])) {
            $fromQuery = $this->getFromQuery($count_mode);

            $generator = $this->getGeneratorData();

            if ($generator !== null) {
                // Use generator as datasource
                $fromQuery = $generator[self::GENERATOR_SOURCE];
            }

            if ($fromQuery !== null) {
                if (
                    !empty($this->_dataset[self::DATA_COUNT_FIELDS])
                    && $count_mode == static::FOR_DISPLAY_COUNT
                ) {
                    $this->lastQuerySQL = "SELECT " . $this->_dataset[self::DATA_COUNT_FIELDS];
                } else {
                    $this->lastQuerySQL = "SELECT COUNT(*)";
                }

                $this->lastQuerySQL .= " $fromQuery";

                $query = $this->connection->query($this->lastQuerySQL);

                if (
                    $query
                    && ($count = $query->fetchColumn())
                    && $count > 0
                ) {
                    // increse count by heading line
                    $this->_count[$count_mode] = (int) $count + 1;
                } else {
                    $this->_count[$count_mode] = 0;
                }
            }
        }

        return $this->_count[$count_mode] ?? null;
    }

    // }}} </editor-fold>
}
