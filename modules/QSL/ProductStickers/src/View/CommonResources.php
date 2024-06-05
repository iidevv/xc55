<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * @param null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        $list[static::RESOURCE_JS][]  = 'modules/QSL/ProductStickers/script.js';
        $list[static::RESOURCE_CSS][] = [
            'file'  => 'modules/QSL/ProductStickers/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less'
        ];

        return $list;
    }
}
