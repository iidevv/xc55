<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Import controller
 * @Extender\Mixin
 */
class Import extends \XLite\Controller\Admin\Import
{
    /**
     * @inheritdoc
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (!\QSL\XLSExportImport\Main::hasZipArchive()) {
            \XLite\Core\TopMessage::addWarning('PHP zip extension is not installed on your server. As a result, import is not possible');
        }
    }

    /**
     * @inheritdoc
     */
    protected function getFilesToImport()
    {
        $dirTo = LC_DIR_VAR . \XLite\Logic\Import\Importer::getImportDir();

        if (!\Includes\Utils\FileManager::isExists($dirTo)) {
            \Includes\Utils\FileManager::mkdirRecursive($dirTo);
        }

        $filesToImport = [];
        if (
            $_FILES
            && isset($_FILES['files'])
            && $_FILES['files']['name']
            && $_FILES['files']['name'][0]
            && \Includes\Utils\FileManager::isDirWriteable($dirTo)
        ) {
            $list = glob($dirTo . LC_DS . '*');
            if ($list) {
                foreach ($list as $path) {
                    if (
                        is_file($path)
                        && $this->isAllowedPath($path)
                    ) {
                        \Includes\Utils\FileManager::deleteFile($path);
                    }
                }
            }

            $files = $_FILES['files'];
            foreach ($files['name'] as $key => $name) {
                $path = null;
                if (
                    $name
                    && $files['error'][$key] === UPLOAD_ERR_OK
                ) {
                    $path = \Includes\Utils\FileManager::getUniquePath($dirTo, $name ?: $files['name'][$key]);

                    if (move_uploaded_file($files['tmp_name'][$key], $path)) {
                        if (
                            $this->isAllowedPath($path)
                            || \XLite\Core\Archive::getInstance()->isArchive($path)
                        ) {
                            $filesToImport[] = $path;
                        } else {
                            \XLite\Core\TopMessage::addError(
                                'The "{{file}}" is not CSV or Excel spreadsheet or archive',
                                ['file' => $name]
                            );
                            \Includes\Utils\FileManager::deleteFile($path);
                        }
                    } else {
                        $path = null;
                    }
                }

                if (!$path) {
                    \XLite\Core\TopMessage::addError(
                        'The "{{file}}" file was not uploaded',
                        ['file' => $name]
                    );
                }
            }
        }

        return $filesToImport;
    }

    /**
     * Check path allowed or not
     *
     * @param string $path File path
     *
     * @return bool
     */
    protected function isAllowedPath($path)
    {
        return \Includes\Utils\FileManager::isCSV($path)
            || \QSL\XLSExportImport\Core\Reader::isAllowedPath($path);
    }
}
