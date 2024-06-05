<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\View;

use Qualiteam\SkinActColorSwatchesFeature\Traits\ColorSwatchesTrait;
use XCart\Extender\Mapping\Extender;

/**
 * Cart widget
 * @Extender\Mixin
 */
class Cart extends \XLite\View\Cart
{

    use ColorSwatchesTrait;

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/product/input/controller.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/product/input/style.less';

        return $list;
    }
}