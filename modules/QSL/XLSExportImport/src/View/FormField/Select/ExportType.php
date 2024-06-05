<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\View\FormField\Select;

/**
 * Export file type
 */
class ExportType extends \XLite\View\FormField\Select\Regular
{
    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        $list = [
            'csv' => 'CSV',
        ];
        foreach (\QSL\XLSExportImport\Core\Writer::getAllowedTypes() as $type) {
            switch ($type) {
                case \QSL\XLSExportImport\Core\Writer::TYPE_XLS:
                    $list[$type] = 'Excel 5';
                    break;

                case \QSL\XLSExportImport\Core\Writer::TYPE_XLSX:
                    $list[$type] = 'Excel 2007';
                    break;

                case \QSL\XLSExportImport\Core\Writer::TYPE_ODS:
                    $list[$type] = 'Open Document';
                    break;
            }
        }

        return $list;
    }
}
