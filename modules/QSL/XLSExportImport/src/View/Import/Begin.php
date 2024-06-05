<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\View\Import;

use XCart\Extender\Mapping\Extender;

/**
 * Begin section
 * @Extender\Mixin
 */
class Begin extends \XLite\View\Import\Begin
{
    /**
     * @inheritdoc
     */
    protected function getUploadFileMessage()
    {
        return \QSL\XLSExportImport\Main::hasZipArchive()
            ? static::t(
                'CSV or Excel spreadsheet or ZIP files, total max size: {{size}}',
                ['size' => ini_get('upload_max_filesize')]
            )
            : parent::getUploadFileMessage();
    }
}
