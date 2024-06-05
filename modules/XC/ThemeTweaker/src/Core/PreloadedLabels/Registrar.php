<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\PreloadedLabels;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * @Extender\Mixin
 */
class Registrar extends \XLite\Core\PreloadedLabels\Registrar
{
    public function register(array $data)
    {
        if (ThemeTweaker::getInstance()->isInLabelsMode()) {
            $data = array_map(static function ($item) {
                return (string) $item;
            }, $data);
        }

        parent::register($data);
    }
}
