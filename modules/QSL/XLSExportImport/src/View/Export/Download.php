<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\View\Export;

use XCart\Extender\Mapping\Extender;

/**
 * Download files box
 * @Extender\Mixin
 */
class Download extends \XLite\View\Export\Download
{
    /**
     * @inheritdoc
     */
    protected function getDownloadFiles()
    {
        $result = [];

        if ($this->getGenerator()) {
            $extensions = \QSL\XLSExportImport\Core\Writer::getExtensions();
            $extensions[] = 'csv';

            foreach ($this->getGenerator()->getDownloadableFiles() as $path) {
                if (preg_match('/\.(' . implode('|', $extensions) . ')$/Ss', $path)) {
                    $key = basename($path);
                    $result[$key] = new \SplFileInfo($path);
                }
            }
        }

        return $result;
    }
}
