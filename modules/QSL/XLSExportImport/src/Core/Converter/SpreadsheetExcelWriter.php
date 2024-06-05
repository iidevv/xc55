<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core\Converter;

use XLite\InjectLoggerTrait;

/**
 * Spreadsheet_Excel_Writer based converter
 */
class SpreadsheetExcelWriter extends \QSL\XLSExportImport\Core\Converter\AConverter
{
    use InjectLoggerTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        set_include_path(get_include_path() . PATH_SEPARATOR . LC_DIR_MODULES . 'QSL/XLSExportImport/lib/PEAR');
        require_once 'Spreadsheet/Excel/Writer.php';

        /*
        if (!class_exists('PEAR')) {
            require_once LC_DIR_MODULES . 'QSL/XLSExportImport/lib/PEAR-1.10.5/PEAR.php';
        }
        if (!class_exists('OLE')) {
            require_once LC_DIR_MODULES . 'QSL/XLSExportImport/lib/OLE-1.0.0RC3/OLE.php';
        }
        if (!class_exists('Spreadsheet_Excel_Writer')) {
            require_once LC_DIR_MODULES . 'QSL/XLSExportImport/lib/Spreadsheet_Excel_Writer-0.9.4/Spreadsheet/Excel/Writer.php';
        }
        */
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
        return [static::TYPE_XLS];
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
        $workbook = new \Spreadsheet_Excel_Writer($path);
        $workbook->setVersion(8);

        /** @var \Spreadsheet_Excel_Writer_Worksheet $worksheet */
        $worksheet = $workbook->addWorksheet('Export');
        $worksheet->setInputEncoding('UTF-8');

        $headerFormat = $workbook->addFormat(['Bold' => true]);

        $widths = [];

        $index = 0;
        $i = 0;
        foreach ($this->headers as $v) {
            $worksheet->writeString($index, $i, $v, $headerFormat);
            $widths[$i] = strlen($v);
            $i++;
        }
        $index++;

        $dateFormat = $workbook->addFormat(['NumFormat' => 'D/M/YYYY h:mm:ss']);
        $seconds_in_a_day = 86400;
        $ut_to_ed_diff = $seconds_in_a_day * 25569;

        while ($row = $this->readRow($fp)) {
            $i = 0;
            $this->getLogger('QSL-XLSExportImport')->debug('', $row);
            foreach ($row as $v) {
                $widths[$i] = max(strlen($v), $widths[$i]);
                if (preg_match('/^[a-zA-Z]+, \d+ [a-zA-Z]+ \d{4} \d+:\d+:\d+ .\d+$/Ssi', $v)) {
                    $v = strtotime($v);
                    $worksheet->writeNumber($index, $i, ($v + $ut_to_ed_diff) / $seconds_in_a_day, $dateFormat);
                } elseif (preg_match('/^(0{1,})([1-9]\d*)$/Ss', $v, $match)) {
                    $worksheet->write($index, $i, ' ' . $v);
                } else {
                    $worksheet->write($index, $i, $v);
                }
                $i++;
            }
            $index++;
        }

        foreach ($widths as $i => $width) {
            $worksheet->setColumn($i, $i, $width);
        }

        $result = $workbook->close();

        return $result === true;
    }
}
