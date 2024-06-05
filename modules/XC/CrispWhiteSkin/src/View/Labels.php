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
class Labels extends \XLite\View\Labels
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            $this->getDir() . '/style.less'
        ];
    }
}
