<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor;

use Doctrine\DBAL\Driver\PDOStatement;

/**
 * Abstract Requirement
 */
abstract class ARequirement
{
    /**
     * Supported platforms
     *
     * @var string[]|array[]
     */
    protected $supported_platforms = [];

    /**
     * Skiped methods in brief list
     *
     * @var array
     */
    protected $skiped_brief_methods = [
        'getNamespaceName' => true,
        'getPlatform'      => true,
        'getVersion'       => true,
        'getLocalOrRemote' => true,
        'isSupported'      => true,
        'isDecryptable'    => true,
        'getBriefList'     => true,
        'getBriefInfo'     => true,
        'getStepConnect'   => true,
    ];

    /**
     * Prepare text list
     *
     * @param PDOStatement $query
     * @param string       $fieldName
     * @param callable     $formatFunction
     *
     * @return string
     */
    protected function prepareTextList($query, $fieldName = 'name', $formatFunction = null)
    {
        $textList = '';

        while ($query && ($result = $query->fetch())) {
            $textList .= !empty($formatFunction) && is_callable($formatFunction)
                ? $formatFunction($result[$fieldName]) . ', '
                : $result[$fieldName] . ', ';
        }

        if (!empty($textList)) {
            $textList = substr($textList, 0, -2);
        }

        return $this->replaceSignHolders($textList);
    }

    /**
     * Replace special sign holders
     *
     * @param string $line
     *
     * @return string
     */
    protected function replaceSignHolders($line)
    {
        return str_replace(['##R##', '##TM##'], ['&reg;', '&trade;'], $line);
    }

    /**
     * Get method label
     *
     * @param string $method
     *
     * @return string method label
     */
    protected function getMethodLabel($method)
    {
        return ucfirst(str_replace(
            ['get_', '_count', '_'],
            ['', '', ' '],
            \XLite\Core\Converter::convertFromCamelCase($method)
        ));
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
            self::$stepConnect = \XLite::getController()->getWizard()->getStep('Connect');
        }

        return self::$stepConnect;
    }

    /**
     * Get connection
     *
     * @return \PDO|bool
     */
    protected function getConnection()
    {
        if ($connectStep = static::getStepConnect()) {
            return $connectStep->getConnection();
        }

        return false;
    }

    /**
     * Get table prefix
     *
     * @return string|bool
     */
    protected function getTablePrefix()
    {
        if ($connectStep = static::getStepConnect()) {
            return $connectStep->getPrefix();
        }

        return false;
    }

    /**
     * Returns random field value from the given table
     *
     * @return array|false
     */
    protected function getRandomRecordsFromTable($field, $table, $condition = "avail = 'Y'", $limit = 5)
    {
        $result = false;

        $tp = $this->getTablePrefix();

        $sql = "SELECT {$field} FROM {$tp}{$table} WHERE $condition ORDER BY RAND() LIMIT {$limit}";

        if (
            $this->getConnection()
            && ($record = $this->getConnection()->query($sql))
        ) {
            $result = $record->fetchAll(\PDO::FETCH_COLUMN);
        }

        return $result;
    }

    /**
     * Get namespace name
     *
     * @return string
     */
    public function getNamespaceName()
    {
        return (new \ReflectionObject($this))->getNamespaceName();
    }

    /**
     * Get XC4 platform name
     */
    abstract public function getPlatform(): string;

    /**
     * Get platform version
     *
     * @return string
     */
    abstract public function getVersion();

    /**
     * Check database
     *
     * @return true if database is supported
     */
    public function isSupported()
    {
        $platform = $this->getPlatform();
        $version  = $this->getVersion();

        if (array_key_exists($platform, $this->supported_platforms)) {
            $supportedVersion = $this->supported_platforms[$platform];
            if (is_array($supportedVersion)) {
                foreach ($supportedVersion as $supported_version) {
                    if (version_compare($version, $supported_version) >= 0) {
                        return true;
                    }
                }
            } elseif (
                is_string($supportedVersion)
                && version_compare($version, $supportedVersion) >= 0
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if decryption is available or not
     *
     * @return boolean
     */
    public function isDecryptable()
    {
        return true;
    }

    /**
     * Get brief info functions list
     *
     * @return array
     */
    public function getBriefList()
    {
        $list = [];

        $methods = (new \ReflectionObject($this))->getMethods(\ReflectionProperty::IS_PUBLIC);

        foreach ($methods as $method) {
            if (!isset($this->skiped_brief_methods[$method->name])) {
                $info          = [
                    'name'  => $method->name,
                    'label' => \XLite\Core\Translation::lbl($this->getMethodLabel($method->name)),
                ];
                $imagesPreview = $method->name . 'ImagesPreview';
                if (method_exists($this, $imagesPreview)) {
                    $info += [
                        'images' => $this->$imagesPreview(),
                    ];

                    $this->skiped_brief_methods[$imagesPreview] = true;
                }
                $list[] = $info;
            }
        }

        return $list;
    }

    /**
     * Return brief method result
     *
     * @param string $method
     *
     * @return string method result
     */
    public function getBriefInfo($method)
    {
        return method_exists($this, $method)
            ? $this->{$method}()
            : '';
    }
}
