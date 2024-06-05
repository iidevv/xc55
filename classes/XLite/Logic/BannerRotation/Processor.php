<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\BannerRotation;

use XLite\InjectLoggerTrait;

/**
 * BannerRotation processor: upload, paths, etc
 */
class Processor extends \XLite\Logic\ALogic
{
    use InjectLoggerTrait;

    protected $images;

    /**
     * Get images directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return LC_DIR_VAR . 'images' . LC_DS . 'banner_rotation' . LC_DS;
    }

    protected function __construct()
    {
        parent::__construct();

        $this->createDirectory();
    }

    /**
     * Creates if not exists
     *
     * @return void
     */
    public function createDirectory()
    {
        $dir = $this->getDirectory();

        if (!\Includes\Utils\FileManager::isExists($dir)) {
            \Includes\Utils\FileManager::mkdirRecursive($dir);
        }
    }

    /**
     * Update custom images
     *
     * @param string $uploadKey Upload key in $_FILES array
     *
     * @return void
     */
    public function uploadImages($uploadKey)
    {
        $dir = $this->getDirectory();

        if (!\Includes\Utils\FileManager::isDirWriteable($dir)) {
            \XLite\Core\TopMessage::addError(
                'The directory {{dir}} does not exist or is not writable.',
                [
                    'dir' => $dir
                ]
            );

            return;
        }

        if (
            $_FILES
            && $_FILES[$uploadKey]
            && $_FILES[$uploadKey]['name']
        ) {
            foreach ($_FILES[$uploadKey]['name'] as $i => $data) {
                \Includes\Utils\FileManager::moveUploadedFileByMultiple($uploadKey, $i, $dir);
            }
        }
    }

    /**
     * Delete images
     *
     * @param array $toDelete Images names to delete
     *
     * @return void
     */
    public function deleteImages(array $toDelete)
    {
        if ($toDelete) {
            foreach ($toDelete as $fileName => $deleteValue) {
                if ($deleteValue) {
                    \Includes\Utils\FileManager::deleteFile(
                        $this->getDirectory() . $fileName
                    );
                }
            }
        }
    }

    /**
     * Get iterator for template files
     *
     * @return \Includes\Utils\FileFilter
     */
    protected function getImagesIterator()
    {
        return new \Includes\Utils\FileFilter(
            $this->getDirectory()
        );
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        if (!isset($this->images)) {
            $this->images = [];
            try {
                foreach ($this->getImagesIterator()->getIterator() as $file) {
                    if ($file->isFile()) {
                        $this->images[] = \Includes\Utils\FileManager::getRelativePath($file->getPathname(), $this->getDirectory());
                    }
                }
            } catch (\Exception $e) {
                $this->getLogger()->debug($e->getMessage());
            }
        }

        return $this->images;
    }
}
