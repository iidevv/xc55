<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core\Converter;

/**
 * Abstract converter
 */
abstract class AConverter extends \XLite\Base
{
    public const TYPE_XLS  = \QSL\XLSExportImport\Core\Writer::TYPE_XLS;
    public const TYPE_XLSX = \QSL\XLSExportImport\Core\Writer::TYPE_XLSX;
    public const TYPE_ODS  = \QSL\XLSExportImport\Core\Writer::TYPE_ODS;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string[]
     */
    protected $headers;

    /**
     * @return string[]
     */
    abstract public function getTypes();

    /**
     * @param resource $fp
     * @param string   $path
     * @param string   $type
     *
     * @return boolean
     */
    abstract protected function doConvert($fp, $path, $type);

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * @param string $from_path
     * @param string $to_path
     * @param string $type
     *
     * @return boolean
     */
    public function convert($from_path, $to_path, $type = null)
    {
        if (!$type) {
            $types = $this->getTypes();
            $type = reset($types);
        }

        $this->type = $type;

        $fp = fopen($from_path, 'rb');
        $this->headers = fgetcsv($fp, null, ',');
        $result = $this->doConvert($fp, $to_path, $type);
        fclose($fp);

        return $result;
    }

    /**
     * @param resource $fp
     *
     * @return array
     */
    protected function readRow($fp)
    {
        $result = fgetcsv($fp, null, ',');
        if ($result) {
            $result = array_combine($this->headers, $result);
        }

        return $result ?: null;
    }
}
