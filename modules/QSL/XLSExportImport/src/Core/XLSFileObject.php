<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Core;

/**
 * XLS file object
 */
class XLSFileObject extends \SplFileObject implements \Countable
{
    /**
     * Position
     *
     * @var integer
     */
    protected $position = 0;

    /**
     * Total number of rows
     *
     * @var integer
     */
    protected $count;

    /**
     * Total number of columns
     *
     * @var integer
     */
    protected $width;

    /**
     * Reader
     *
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected $reader;

    /**
     * Open
     */
    public function open()
    {
        /** @var \PhpOffice\PhpSpreadsheet\Reader\IReader $reader */
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($this->getPathname());
        $reader->setReadDataOnly(true);

        $this->reader = $reader->load($this->getPathname());
        $sheet = $this->reader->setActiveSheetIndex(0);

        // Count
        $this->count = $sheet->getHighestRow();

        // Width
        $rowInternal = $sheet->getRowIterator(1)->current();
        $cellIterator = $rowInternal->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        $this->width = iterator_count($cellIterator);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        $rowInternal = $this->reader->getActiveSheet()->getRowIterator($this->position + 1)->current();
        $cellIterator = $rowInternal->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $row = [];
        foreach ($cellIterator as $cell) {
            /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
            $row[] = $cell->getValue() ?? '';
        }

        // Normalize row's width
        if (count($row) > $this->width) {
            $row = array_slice($row, 0, $this->width);
        } elseif (count($row) < $this->width) {
            for ($i = count($row); $i < $this->width; $i++) {
                $row[] = '';
            }
        }

        return $row;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        do {
            $this->position++;
        } while ($this->valid() && $this->isEmptyRow($this->current()));
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->count > $this->position;
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function seek($position)
    {
        $this->position = $position;
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        return !$this->valid();
    }

    /**
     * Check - row is empty or not
     *
     * @param string[] $row Data row
     *
     * @return boolean
     */
    protected function isEmptyRow(array $row)
    {
        $row = array_filter(
            $row,
            static function ($elm) {
                return trim($elm) !== '';
            }
        );

        return count($row) === 0;
    }
}
