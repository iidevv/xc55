<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract import processor
 * @Extender\Mixin
 */
abstract class AProcessor extends \XLite\Logic\Import\Processor\AProcessor
{
    /**
     * XLS file or not
     *
     * @var boolean
     */
    protected $is_xls = false;

    /**
     * @inheritdoc
     */
    protected function getRawFile($path)
    {
        return \QSL\XLSExportImport\Core\Reader::isAllowedPath($path)
            ? $this->getXLSRawFile($path)
            : parent::getRawFile($path);
    }

    /**
     * Get XLS raw file object
     *
     * @param string $path File path
     *
     * @return \QSL\XLSExportImport\Core\XLSFileObject
     */
    protected function getXLSRawFile($path)
    {
        try {
            $file = new \QSL\XLSExportImport\Core\XLSFileObject($path, 'rb');
            $this->is_xls = true;
            $file->open();
        } catch (\Exception $e) {
            $file = null;
        }

        return $file;
    }

    /**
     * @inheritdoc
     */
    protected function verifyValueAsDate($value)
    {
        if ($this->is_xls && preg_match('/^[\d\.]+$/Ss', $value)) {
            $result = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
            $tz = date_default_timezone_get();
            if ($tz !== 'UTC') {
                $adjustment = \PhpOffice\PhpSpreadsheet\Shared\TimeZone::getTimeZoneAdjustment($tz, $result) * -1;
                $result += $adjustment;
            }
        } else {
            $result = parent::verifyValueAsDate($value);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function normalizeValueAsDate($value)
    {
        if ($this->is_xls && preg_match('/^[\d\.]+$/Ss', $value)) {
            $result = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);
            $tz = date_default_timezone_get();
            if ($tz !== 'UTC') {
                $adjustment = \PhpOffice\PhpSpreadsheet\Shared\TimeZone::getTimeZoneAdjustment($tz, $result) * -1;
                $result += $adjustment;
            }
        } else {
            $result = parent::normalizeValueAsDate($value);
        }

        return $result;
    }
}
