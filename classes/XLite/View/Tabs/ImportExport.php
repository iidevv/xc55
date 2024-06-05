<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class ImportExport extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'import';
        $list[] = 'export';

        return $list;
    }

    protected function isImportAllowed(): bool
    {
        return Auth::getInstance()->isPermissionAllowed('manage import');
    }

    protected function isExportAllowed(): bool
    {
        return Auth::getInstance()->isPermissionAllowed('manage export');
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $result = [];

        if ($this->isImportAllowed()) {
            $result['import'] = [
                'weight' => 100,
                'title'  => static::t('CSV Import'),
                'widget' => 'XLite\View\Page\Admin\Import',
            ];
        }

        if ($this->isExportAllowed()) {
            $result['export'] = [
                'weight' => 200,
                'title'  => static::t('CSV Export'),
                'widget' => 'XLite\View\Page\Admin\Export',
            ];
        }

        return $result;
    }
}
