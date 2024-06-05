<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\FlyoutCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * @param boolean $adminZone Admin zone flag OPTIONAL
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (!($adminZone ?? \XLite::isAdminZone())) {
            $list[static::RESOURCE_JS][]  = 'modules/QSL/FlyoutCategoriesMenu/flyout-menu.js';

            $list[static::RESOURCE_CSS][] = [
                'file'  => 'modules/QSL/FlyoutCategoriesMenu/flyout-menu.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }
}
