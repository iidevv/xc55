<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    protected function getThemeFiles($adminZone = null)
    {
        $list = parent::getThemeFiles($adminZone);

        if (!($adminZone ?? \XLite::isAdminZone())) {
            $list[static::RESOURCE_JS][] = 'js/bootstrap-tabcollapse.js';
            $list[static::RESOURCE_JS][] = 'js/jquery.collapser.js';
            $list[static::RESOURCE_JS][] = [
                'file'      => 'js/jquery.actual.min.js',
                'no_minify' => true,
            ];
            $list[static::RESOURCE_JS][] = 'js/jquery.floating-label.js';
            $list[static::RESOURCE_JS][] = 'js/jquery.path.js';
            $list[static::RESOURCE_JS][] = 'js/jquery.fly.js';
            $list[static::RESOURCE_JS][] = 'js/utils.js';
            $list[static::RESOURCE_JS][] = 'js/header.js';
            $list[static::RESOURCE_JS][] = 'js/footer.js';
        }

        return $list;
    }
}
