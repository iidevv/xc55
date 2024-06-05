<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\View\Export;

use XCart\Extender\Mapping\Extender;

/**
 * Begin section
 * @Extender\Mixin
 */
class Begin extends \XLite\View\Export\Begin
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/XLSExportImport/export/begin.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/XLSExportImport/export/begin.js';

        return $list;
    }
}
