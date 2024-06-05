<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to export page
 * @Extender\Mixin
 */
class Export extends \XLite\View\Tabs\Export
{
    /**
     * Check download files available or not
     *
     * @return boolean
     */
    protected function downloadFilesAvailable()
    {
        return $this->getGenerator()
            ? (bool) $this->getGenerator()->getDownloadableFiles()
            : false;
    }
}
