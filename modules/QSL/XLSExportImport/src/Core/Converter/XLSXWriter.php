<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core\Converter;

/**
 * XLSXWriter based converter
 */
class XLSXWriter extends \QSL\XLSExportImport\Core\Converter\AConverter
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        require_once LC_DIR_MODULES . 'QSL/XLSExportImport/lib/xlsxwriter.class.php';
    }

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {
        return parent::isAvailable()
            && \QSL\XLSExportImport\Main::hasZipArchive();
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
        return [static::TYPE_XLSX];
    }

    /**
     * @param resource $fp
     * @param string   $path
     * @param string   $type
     *
     * @return boolean
     */
    protected function doConvert($fp, $path, $type)
    {
        $writer = new \XLSXWriter();

        $headers = [];
        $headersLength = count($this->headers);

        for ($i = 0; $i < $headersLength; $i++) {
            $headers[] = 'string';
        }

        while ($row = $this->readRow($fp)) {
            $i = 0;
            foreach ($row as $k => $v) {
                if (preg_match('/^[a-zA-Z]+, \d+ [a-zA-Z]+ \d{4} \d+:\d+:\d+ .\d+$/Ssi', $v)) {
                    $v = strtotime($v);
                    $v = date('Y-m-d H:i:s', $v);
                    if (isset($headers) && $headers[$i] === 'string') {
                        $headers[$i] = 'YYYY-MM-DD HH:MM:SS';
                    }
                }
                $row[$k] = $v;
                $i++;
            }

            if (isset($headers)) {
                $writer->writeSheetRow('Export', array_values($this->headers), ['font-style' => 'bold']);
                $writer->writeSheetHeader('Export', $headers, true);
                unset($headers);
            }

            $writer->writeSheetRow('Export', array_values($row));
        }

        $writer->writeToFile($path);

        return true;
    }
}
