<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract export step
 * @Extender\Mixin
 */
abstract class AStep extends \XLite\Logic\Export\Step\AStep
{
    public const TEMPORARY_SUFFIX = '.temporary';

    /**
     * Writer
     *
     * @var \QSL\XLSExportImport\Core\Writer
     */
    protected $writer;

    /**
     * Write header flag
     *
     * @var boolean
     */
    protected $write_header = false;

    /**
     * Close XLS writer
     */
    public function closeXLSWriter()
    {
        if ($this->getWriter()) {
            $this->getWriter()->close();
        }
    }

    /**
     * @inheritdoc
     */
    public function finalize()
    {
        parent::finalize();

        if ($this->getWriter()) {
            $this->getWriter()->convert();
        }
    }

    /**
     * @inheritdoc
     */
    protected function buildHeader()
    {
        $this->write_header = true;
        parent::buildHeader();
        $this->write_header = false;
    }

    /**
     * @inheritdoc
     */
    protected function write(array $row)
    {
        if ($this->getWriter()) {
            $result = $this->write_header
                ? $this->getWriter()->writeHeader($row)
                : $this->getWriter()->writeRow($this->prepareWriterValues($this->normalizeXLSRow($row)));

            if ($result === false) {
                $this->generator->addError(
                    static::t('Failed write to file'),
                    static::t('Failed write to file X. There may not be enough disc-space. Please check if there is enough disc-space.', ['path' => $this->filePath])
                );
            }
        } else {
            $result = parent::write($row);
        }

        return $result;
    }

    /**
     * Normalize row (for XLS)
     *
     * @param array $row Row
     *
     * @return string[]
     */
    protected function normalizeXLSRow(array $row)
    {
        $result = [];

        foreach (array_keys($this->getColumns()) as $name) {
            $result[$name] = $row[$name] ?? '';
        }

        return $result;
    }

    /**
     * Get writer
     *
     * @return \QSL\XLSExportImport\Core\Writer|false
     */
    protected function getWriter()
    {
        if (!isset($this->writer)) {
            if (\QSL\XLSExportImport\Core\Writer::isAcceptType($this->generator->getOptions()->type)) {
                $this->writer = new \QSL\XLSExportImport\Core\Writer(
                    $this->generator->getOptions()->type,
                    $this->getWriterFilename($this->generator->getOptions()->type),
                    $this->getWriterOptions()
                );
                if (!$this->writer->isAvailable()) {
                    $this->writer = false;
                }
            } else {
                $this->writer = false;
            }
        }

        return $this->writer;
    }

    /**
     * Get writer file path
     *
     * @param string $type File type
     *
     * @return null|string
     */
    protected function getWriterFilename($type)
    {
        $this->filePath = null;

        $name = $this->getFilename();
        if (substr($name, -4) === '.csv') {
            $name = substr($name, 0, -4);
        }

        $name .= '.' . \QSL\XLSExportImport\Core\Writer::getExtension($type);
        $name = preg_replace('/(\.[^\.]+)$/', '-' . date('Y-m-d') . '$1', $name);
        $name .= static::TEMPORARY_SUFFIX;

        $dir = \Includes\Utils\FileManager::getRealPath(LC_DIR_VAR . $this->generator->getOptions()->dir);
        if (!file_exists($dir)) {
            \Includes\Utils\FileManager::mkdirRecursive($dir);
        }

        if (is_writable($dir)) {
            $this->filePath = $dir . LC_DS . $name;
        } else {
            $this->generator->addError(
                static::t('Directory does not have permissions to write'),
                static::t('Directory X does not have permissions to write. Please set necessary permissions to directory X.', ['path' => $dir])
            );
        }

        return $this->filePath;
    }

    /**
     * Get writer options
     *
     * @return mixed[]
     */
    protected function getWriterOptions()
    {
        $parts = explode('\\', get_called_class());
        $name = array_pop($parts);

        return [
            \QSL\XLSExportImport\Core\Writer::OPTION_TITLE => ucfirst($name),
            'position'                                                  => $this->generator->getOptions()->xls_position ?? 0,
        ];
    }

    /**
     * Prepare values for writer
     *
     * @param array $row Data row
     *
     * @return array
     */
    protected function prepareWriterValues(array $row)
    {
        return $row;
    }

    /**
     * Get writes types
     *
     * @return array[]
     */
    protected function getWriterTypes()
    {
        return [
            'integers'   => [],
            'floats'     => [],
            'currencies' => [],
            'dates'      => [],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function closeWriter()
    {
        $this->closeXLSWriter();

        parent::closeWriter();
    }
}
