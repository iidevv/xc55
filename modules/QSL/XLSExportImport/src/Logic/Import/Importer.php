<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Import;

use XCart\Extender\Mapping\Extender;

/**
 * Importer
 * @Extender\Mixin
 */
class Importer extends \XLite\Logic\Import\Importer
{
    /**
     * @inheritdoc
     */
    public function getCSVList()
    {
        if (!isset($this->csvFilter)) {
            $dir = \Includes\Utils\FileManager::getRealPath(LC_DIR_VAR . $this->getOptions()->dir);

            $this->csvFilter = new \Includes\Utils\FileFilter(
                $dir,
                '/\.(?:csv|' . implode('|', \QSL\XLSExportImport\Core\Writer::getExtensions()) . ')$/Ss'
            );
        }

        return $this->csvFilter->getIterator();
    }
}
