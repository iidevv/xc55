<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export;

/**
 * Generator
 */
class Generator extends \XLite\Logic\AGenerator
{
    /**
     * Default export directory
     */
    public const EXPORT_DIR = 'export';

    /**
     * Default export cycle length
     */
    public const DEFAULT_CYCLE_LENGTH = 50;

    /**
     * Max. file size for archieving
     */
    public const MAX_FILE_SIZE = 50000000;

    /**
     * Default export process tick duration
     */
    public const DEFAUL_TICK_DURATION = 0.5;

    /**
     * Language code
     *
     * @var string
     */
    protected static $languageCode;

    /**
     * Flag: is export in progress (true) or no (false)
     *
     * @var boolean
     */
    protected static $inProgress = false;

    /**
     * Set inProgress flag value
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setInProgress($value)
    {
        static::$inProgress = $value;
    }

    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     */
    public function __construct(array $options = [])
    {
        $delimiter = $options['delimiter'] ?? ',';
        if ($delimiter == 'tab') {
            $delimiter = "\t";
        }

        $this->options = [
            'position'  => isset($options['position']) ? intval($options['position']) + 1 : 0,
            'include'   => $options['include'] ?? [],
            'charset'   => $options['charset'] ?? 'UTF-8',
            'delimiter' => $delimiter,
            'enclosure' => $options['enclosure'] ?? '"',
            'errors'    => $options['errors'] ?? [],
            'warnings'  => $options['warnings'] ?? [],
            'dir'       => $options['dir'] ?? static::EXPORT_DIR,
            'copyResources' => $options['copyResources'] ?? true,
            'attrs'     => $options['attrs'] ?? 'all',
            'time'      => isset($options['time']) ? intval($options['time']) : 0,
            'selection'   => $options['selection'] ?? [],
            'filter'      => $options['filter'] ?? '',
            'isAttrHeaderBuilt' => isset($options['isAttrHeaderBuilt']) ? (bool)$options['isAttrHeaderBuilt'] : false,
            'itemsList' => $options['itemsList'] ?? false,
        ] + $options;

        static::$languageCode = $options['languageCode'] ?? \XLite\Core\Config::getInstance()->General->default_admin_language;

        $this->options = new \ArrayObject($this->options, \ArrayObject::ARRAY_AS_PROPS);

        if ($this->getOptions()->position == 0) {
            $this->initialize();
        }
    }

    /**
     * Get language code
     *
     * @return string
     */
    public static function getLanguageCode()
    {
        return static::$inProgress ? static::$languageCode : null;
    }

    /**
     * Delete all files
     */
    public function deleteAllFiles()
    {
        if (!$this->getOptions()->dir) {
            return; # XCB-1853
        }
        if (!\Includes\Utils\FileManager::isExists(LC_DIR_VAR . $this->getOptions()->dir)) {
            \Includes\Utils\FileManager::mkdir(LC_DIR_VAR . $this->getOptions()->dir);
        }

        $list = glob(LC_DIR_VAR . $this->getOptions()->dir . '/*');
        if ($list) {
            foreach ($list as $path) {
                if (is_file($path)) {
                    \Includes\Utils\FileManager::deleteFile($path);
                } else {
                    \Includes\Utils\FileManager::unlinkRecursive($path);
                }
            }
        }
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
        $this->deleteAllFiles();
    }

    // {{{ Steps

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            'XLite\Logic\Export\Step\Attributes',
            'XLite\Logic\Export\Step\Categories',
            'XLite\Logic\Export\Step\Products',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueSelect',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueText',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueHidden',
            'XLite\Logic\Export\Step\Users',
            'XLite\Logic\Export\Step\Orders',
        ];
    }

    // }}}

    // {{{ Download files

    /**
     * Get downloadable files
     *
     * @return array
     */
    public function getDownloadableFiles()
    {
        $result = [];

        foreach ($this->getFileIterator() as $filePath => $fileObject) {
            if (is_file($filePath)) {
                $result[] = $filePath;
            }
        }

        return $result;
    }

    /**
     * Get file iterator
     *
     * @return \RecursiveIteratorIterator
     */
    protected function getFileIterator()
    {
        $dir = LC_DIR_VAR . $this->getOptions()->dir . LC_DS;

        if (!\Includes\Utils\FileManager::isExists($dir)) {
            \Includes\Utils\FileManager::mkdirRecursive($dir);
        }

        $dirIterator = new \RecursiveDirectoryIterator(
            $dir,
            \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
        );

        return new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::SELF_FIRST);
    }

    // }}}

    // {{{ Pack files

    /**
     * Check - can pack files with specified archive type
     *
     * @param string $type Archive type
     *
     * @return boolean
     */
    public function canPackFiles($type)
    {
        return \XLite\Core\Archive::getInstance()->isTypeAvailable(strtolower(strval($type)));
    }

    /**
     * Pack export files
     *
     * @param string $type Archive type
     *
     * @return string
     */
    public function packFiles($type)
    {
        $files = $this->getPackedFiles();

        $path = LC_DIR_TMP . 'export-' . $this->getPackExportDate();

        return \XLite\Core\Archive::getInstance()
            ->pack($files, $path, strtolower(strval($type))) ? $path : false;
    }

    /**
     * Get last export date
     *
     * @return string
     */
    protected function getPackExportDate()
    {
        $list = $this->getPackedFiles();
        $file = $list ? new \SplFileInfo(current($list)) : null;
        $date = date('Y-m-d');

        if ($file) {
            $date = \XLite\Core\Converter::formatDate($file->getMTime(), '%Y-%m-%d');
        }

        return $date;
    }

    /**
     * Get allowed archives
     *
     * @return array
     */
    public function getAllowedArchives()
    {
        $result = $this->getAllowedArchiveTypes();

        if (in_array('tar', $result) && function_exists('gzcompress')) {
            $key = array_search('tar', $result);
            unset($result[$key]);
        }

        return array_values($result);
    }

    /**
     * Get allowed archive types
     *
     * @return array
     */
    protected function getAllowedArchiveTypes()
    {
        return \XLite\Core\Archive::getInstance()->getTypes();
    }

    /**
     * Get excluded files
     *
     * @return array
     */
    protected function getExcludedFiles()
    {
        return [
            '.htaccess'
        ];
    }

    /**
     * Check file is allowed to pack or not
     *
     * @param string $filePath File path
     *
     * @return boolean
     */
    protected function isAllowedToPack($filePath)
    {
        return filesize($filePath) < \XLite\Logic\Export\Generator::MAX_FILE_SIZE
            && !in_array(basename($filePath), $this->getExcludedFiles());
    }

    /**
     * Get packed Files
     *
     * @return array
     */
    public function getPackedFiles()
    {
        $result = [];

        foreach ($this->getFileIterator() as $filePath => $fileObject) {
            if ($this->isAllowedToPack($filePath)) {
                $result[] = $filePath;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get export event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'export';
    }

    // }}}
}
