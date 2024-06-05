<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core;

/**
 * Writer
 */
class Writer extends \XLite\Base
{
    /**
     * Types
     */
    public const TYPE_XLS  = 'xls';
    public const TYPE_XLSX = 'xlsx';
    public const TYPE_ODS  = 'ods';

    /**
     * Option names
     */
    public const OPTION_TITLE       = 'title';
    public const OPTION_DESCRIPTION = 'description';

    /**
     * File type
     *
     * @var string
     */
    protected $type;

    /**
     * File path
     *
     * @var string
     */
    protected $path;

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * Writer
     *
     * @var resource
     */
    protected $writer;

    /**
     * @var string[]
     */
    protected static $xls_types = [
        self::TYPE_XLS,
        self::TYPE_XLSX,
        self::TYPE_ODS,
    ];

    /**
     * Check file types availability
     *
     * @param string $type File type
     *
     * @return boolean
     */
    public static function isAcceptType($type)
    {
        return in_array($type, static::getAllowedTypes());
    }

    /**
     * @return string[]
     */
    public static function getAllowedTypes()
    {
        $result = [];

        foreach (static::$xls_types as $type) {
            foreach (static::getConverters() as $class) {
                /** @var \QSL\XLSExportImport\Core\Converter\AConverter $converter */
                $converter = new $class();
                if ($converter->isAvailable() && in_array($type, $converter->getTypes())) {
                    $result[] = $type;
                }
            }
        }

        return $result;
    }

    /**
     * Get extension by type
     *
     * @param string $type File type
     *
     * @return null|string
     */
    public static function getExtension($type)
    {
        switch ($type) {
            case static::TYPE_XLS:
                $result = 'xls';
                break;

            case static::TYPE_XLSX:
                $result = 'xlsx';
                break;

            case static::TYPE_ODS:
                $result = 'ods';
                break;

            default:
                $result = null;
        }

        return $result;
    }

    /**
     * Get extensions list
     *
     * @return string[]
     */
    public static function getExtensions()
    {
        $list = [];

        if (\QSL\XLSExportImport\Main::hasZipArchive()) {
            $list += ['xls', 'xlsx', 'ods'];
        }

        return $list;
    }

    /**
     * Constructor
     *
     * @param string $type    File type
     * @param string $path    File path
     * @param array  $options Options OPTIONAL
     */
    public function __construct($type, $path, array $options = [])
    {
        parent::__construct();

        $this->type = $type;
        $this->path = $path;
        $this->options = $options;

        $this->open();
    }

    /**
     * Check writer availability
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->path
            && (!file_exists($this->path) || is_writable($this->path))
            && static::isAcceptType($this->type);
    }

    /**
     * Open writer
     */
    public function open()
    {
        if (!$this->writer) {
            $this->writer = $this->defineWriter();
        }
    }

    /**
     * Write header
     *
     * @param array $header Header
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function writeHeader(array $header)
    {
        fputcsv($this->writer, $header, ',');
    }

    /**
     * Write data row
     *
     * @param array $data  Data
     */
    public function writeRow(array $data)
    {
        fputcsv($this->writer, $data, ',');
    }

    /**
     * Close writer
     *
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function close()
    {
        if ($this->writer) {
            fclose($this->writer);
            $this->writer = null;
        }
    }

    /**
     * @return boolean
     */
    public function convert()
    {
        $result = false;

        foreach (static::getConverters() as $class) {
            /** @var \QSL\XLSExportImport\Core\Converter\AConverter $converter */
            $converter = new $class();
            if ($converter->isAvailable() && in_array($this->type, $converter->getTypes())) {
                $result = $converter->convert(
                    $this->path,
                    substr($this->path, 0, strlen(\XLite\Logic\Export\Step\AStep::TEMPORARY_SUFFIX) * -1),
                    $this->type
                );
                unlink($this->path);
                break;
            }
        }

        return $result;
    }

    /**
     * @return string[]
     */
    protected static function getConverters()
    {
        return [
            '\QSL\XLSExportImport\Core\Converter\XLSXWriter',
            '\QSL\XLSExportImport\Core\Converter\Spout',
        ];
    }

    /**
     * Define writer
     *
     * @return resource
     */
    protected function defineWriter()
    {
        if (file_exists($this->path)) {
            $writer = fopen($this->path, 'ab');
        } else {
            $writer = fopen($this->path, 'wb');
        }

        return $writer;
    }
}
