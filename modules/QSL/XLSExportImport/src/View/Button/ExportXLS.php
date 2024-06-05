<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\View\Button;

/**
 * Submit button for export products (XLS)
 */
class ExportXLS extends \XLite\View\Button\ExportCSV
{
    /**
     * @inheritdoc
     */
    public function getURLParams()
    {
        $params = parent::getURLParams();
        $params['export']['options']['type'] = 'xls';

        return $params;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultLabel()
    {
        return 'Excel 5';
    }

    /**
     * @inheritdoc
     */
    protected function getClass()
    {
        return parent::getClass() . ' export-xls';
    }
}
