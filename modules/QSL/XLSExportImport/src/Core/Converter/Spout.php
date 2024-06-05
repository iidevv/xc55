<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core\Converter;

/**
 * Spout based converter
 */
class Spout extends \QSL\XLSExportImport\Core\Converter\AConverter
{
    /**
     * @return string[]
     */
    public function getTypes()
    {
        return [static::TYPE_ODS, static::TYPE_XLSX];
    }

    /**
     * @inheritdoc
     */
    public function isAvailable()
    {
        return parent::isAvailable()
            && \QSL\XLSExportImport\Main::hasZipArchive()
            && \QSL\XLSExportImport\Main::hasXMLReader();
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
        /** @var \Box\Spout\Writer\AbstractMultiSheetsWriter $writer */
        switch ($type) {
            case static::TYPE_ODS:
                $writer = \Box\Spout\Writer\WriterFactory::create(\Box\Spout\Common\Type::ODS);
                break;

            case static::TYPE_XLSX:
                $writer = \Box\Spout\Writer\WriterFactory::create(\Box\Spout\Common\Type::XLSX);
                break;
        }

        //$writer->setShouldCreateNewSheetsAutomatically(true);
        $writer->openToFile($path);

        // Styles
        $headerStyle = new \Box\Spout\Writer\Style\StyleBuilder();
        $headerStyle = $headerStyle->setFontBold()
            ->build();

        $writer->addRowWithStyle($this->headers, $headerStyle);

        while ($row = $this->readRow($fp)) {
            foreach ($row as $k => $v) {
                $row[$k] = $v;
            }

            $writer->addRow(array_values($row));
        }

        $writer->close();

        return true;
    }
}
