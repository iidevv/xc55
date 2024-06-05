<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Export controller
 * @Extender\Mixin
 */
class Export extends \XLite\Controller\Admin\Export
{
    /**
     * @inheritdoc
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (!\QSL\XLSExportImport\Main::hasZipArchive()) {
            \XLite\Core\TopMessage::addWarning('PHP zip extension is not installed on your server. As a result, export is not possible');
        }
    }

    /**
     * @inheritdoc
     */
    protected function assembleExportOptions()
    {
        $request = \XLite\Core\Request::getInstance();

        return parent::assembleExportOptions()
            + ['type' => $request->options['type'] ?? 'csv'];
    }
}
