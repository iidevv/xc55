<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Command\CSV;

use Includes\Utils\FileManager;
use Qualiteam\SkinActYotpoReviews\Command\Validator\File;
use Qualiteam\SkinActYotpoReviews\Command\Validator\ValidatorException;

abstract class AFile
{
    protected $fp = null;
    protected string $defaultDelimiter = ',';
    protected string|null $delimiter = null;

    abstract protected function getFileName(): string;

    /**
     * @throws \Qualiteam\SkinActYotpoReviews\Command\CSV\CSVException
     */
    public function initFilePointer(): void
    {
        $name = $this->getFilename();

        if (substr($name, -4) != '.csv') {
            $name .= '.csv';
        }

        $name = preg_replace('/(\.[^\.]+)$/', '-' . date('d-m-Y-H-i-s') . '$1', $name);

        $path = LC_DIR_VAR . $this->getFeedsDir();

        try {
            $validator = new File($path);
            $validator->valid();
        } catch (ValidatorException $e) {
            throw new CSVException($e->getMessage());
        }

        $dir = FileManager::getRealPath($path);
        $this->fp = @fopen($dir . LC_DS . $name, 'ab');
    }

    protected function getFeedsDir(): string
    {
        return 'feeds';
    }

    protected function getDelimiter(): string
    {
        return $this->delimiter ?? $this->defaultDelimiter;
    }

    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    protected function writeIntoFile(array $value): void
    {
        if ($this->fp) {
            fputcsv($this->fp, $value, $this->getDelimiter());
        }
    }

    public function write(array $value): void
    {
        $this->writeIntoFile($value);
    }

    protected function closeWriter(): void
    {
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    public function stop(): void
    {
        $this->closeWriter();
    }
}
