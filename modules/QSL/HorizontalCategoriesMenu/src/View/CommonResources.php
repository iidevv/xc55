<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * Return theme common files
     *
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (!($adminZone ?? \XLite::isAdminZone())) {
            $list[static::RESOURCE_JS][]  = \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_use_multicolumn
                ? 'modules/QSL/HorizontalCategoriesMenu/multicol_menu.js'
                : 'modules/QSL/HorizontalCategoriesMenu/flyout_menu.js';

            $list[static::RESOURCE_CSS][] = [
                'file'  => 'modules/QSL/HorizontalCategoriesMenu/horizontal-flyout-menu.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }
}
