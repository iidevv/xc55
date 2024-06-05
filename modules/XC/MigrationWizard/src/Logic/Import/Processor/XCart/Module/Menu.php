<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

use XLite\InjectLoggerTrait;
use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * SimpleCMS module
 */
class Menu extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    use InjectLoggerTrait;

    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const MENU_LINK_HOME       = '{home}';
    public const MENU_LINK_CART       = '?target=cart';
    public const MENU_LINK_MY_ACCOUNT = '{my account}';
    public const MENU_LINK_CONTACT_US = '?target=contact_us';

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    protected static $defaultLinks = [
        'home.php'                               => self::MENU_LINK_HOME,
        'cart.php'                               => self::MENU_LINK_CART,
        'register.php?mode=update'               => self::MENU_LINK_MY_ACCOUNT,
        'help.php?section=contactus&mode=update' => self::MENU_LINK_CONTACT_US,
    ];

    protected static $defaultType = 'P';

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
            'id'       => [],
            'name'     => [
                static::COLUMN_IS_MULTILINGUAL => true,
            ],
            'link'     => [
                static::COLUMN_IS_KEY => true,
            ],
            'type'     => [],
            'position' => [],
            'enabled'  => [],
        ];
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu');
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineFieldset()
    {
        $type           = static::$defaultType;
        $languageFields = static::getMenuLanguageFieldsSQL();

        return 'sb.id AS `id`,'
            . 'sb.title AS `name`,'
            . $languageFields
            . 'sb.link AS `link`,'
            . "'{$type}' AS `type`,"
            . 'sb.orderby AS `position`,'
            . "IF(sb.active = 'Y', true, false) AS `enabled`";
    }

    /**
     * Define dataset SQL
     *
     * @return string
     */
    public static function defineDataset()
    {
        $dataset = '';

        if (static::defineTemporaryDataset()) {
            $prefix = self::getTablePrefix();

            $dataset = "{$prefix}speed_bar AS sb";
        }

        return $dataset;
    }

    /**
     * Define temporary dataset table SQL
     *
     * @return boolean
     */
    public static function defineTemporaryDataset()
    {
        $result = false;

        $prefix = static::getTablePrefix();

        if (
            ($speedBarData = static::getCellData("SELECT value FROM {$prefix}config WHERE name = 'speed_bar'"))
            && ($speedBar = static::unserializeLatin1($speedBarData))
            && is_array($speedBar)
            && static::createTemporaryTable()
        ) {
            $result                   = true;
            $fillSpeedBarPDOStatement = static::getFillSpeedBarPDOStatement();
            if ($fillSpeedBarPDOStatement) {
                foreach ($speedBar as $item) {
                    $data = [
                        $item['id'],
                        $item['title'],
                        $item['link'],
                        $item['orderby'],
                        $item['active'],
                    ];

                    $result = $fillSpeedBarPDOStatement->execute($data);

                    if (!$result) {
                        static::getStaticLogger('migration_errors')->error('', [
                            'Error' => 'Failed to insert data into temporary table!',
                            'Data'  => $data,
                            'Info'  => $fillSpeedBarPDOStatement->errorInfo(),
                        ]);

                        \XC\MigrationWizard\Logic\Migration\Wizard::registerTransferDataError();

                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected static function createTemporaryTable()
    {
        $prefix = self::getTablePrefix();

        $query = static::getConnection()->query(
            "CREATE TEMPORARY TABLE IF NOT EXISTS {$prefix}speed_bar ("
            . ' id int(11) NOT NULL AUTO_INCREMENT,'
            . ' title varchar(255) NOT NULL DEFAULT "",'
            . ' link varchar(255) NOT NULL DEFAULT "",'
            . ' orderby mediumint(11) NOT NULL DEFAULT "0",'
            . ' active char(1) NOT NULL DEFAULT "Y",'
            . ' PRIMARY KEY (id)'
            . ') ENGINE=MyISAM DEFAULT CHARSET=utf8;'
        );

        return $query && $query->execute();
    }

    /**
     * @return bool|\PDOStatement
     */
    protected static function getFillSpeedBarPDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            "REPLACE INTO {$prefix}speed_bar VALUES (?, ?, ?, ?, ?)"
        );
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['CDev\SimpleCMS'];
    }

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get category language fields SQL
     *
     * @return string
     */
    public static function getMenuLanguageFieldsSQL()
    {
        return static::getLanguageFieldsSQLfor(
            [
                'sb.`title`' => 'name',
            ],
            Configuration::getAvailableLanguages()
        );
    }

    /**
     * Get title to clarify which entity is migrating
     *
     * @return string
     */
    public function getProcessorMigratingTitle()
    {
        return static::t('Migrating menu items');
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    /**
     * Normalize 'name' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeNameValue($value)
    {
        $result = $value;
        $nameValuePDOStatement = $this->getNameValuePDOStatement();

        if (
            $nameValuePDOStatement
            && $nameValuePDOStatement->execute([$this->currentRowData['id']])
            && ($found = $nameValuePDOStatement->fetchAll(\PDO::FETCH_KEY_PAIR))
        ) {
            $default = $this->normalizeValueAsString($this->getDefLangValue($value));
            $keys = array_keys($value);

            foreach ($keys as $k) {
                $result[$k] = !empty($found[$k]) && ($found[$k] !== $default) ? $found[$k] : $default;
            }
        }

        return $result;
    }

    /**
     * @return bool|\PDOStatement
     */
    protected function getNameValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT code, value'
            . " FROM {$prefix}languages_alt"
            . ' WHERE name = ?'
        );
    }

    /**
     * Normalize 'link' value
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function normalizeLinkValue($value)
    {
        if (!empty(static::$defaultLinks[$value])) {
            return static::$defaultLinks[$value];
        }

        $result = $value;

        $matches = [];

        if (preg_match('/pages\.php\?pageid=([0-9]+)/iSs', $value, $matches)) {
            $entry = static::getEntryFromRegistryByClassAndSourceId('CDev\SimpleCMS\Model\Page', $value);
            if ($entry) {
                $result = '?target=page&id=' . $entry->getResultId();
            }
        }

        return $result;
    }

    // }}} </editor-fold>

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    public static function hasTransferableData()
    {
        static $result = null;

        if ($result === null) {
            $prefix = self::getTablePrefix();
            $result = false;

            if (
                ($speedBarData = static::getCellData("SELECT value FROM {$prefix}config WHERE name = 'speed_bar'"))
                && ($speedBar = static::unserializeLatin1($speedBarData))
                && is_array($speedBar)
            ) {
                $result = (bool) count($speedBar);
            }
        }

        return $result;
    }

    // }}} </editor-fold>
}
