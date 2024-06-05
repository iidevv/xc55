<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View;

use XCart\Extender\Mapping\Extender;

/**
 * Tabber
 * @Extender\Mixin
 */
class Tabber extends \XLite\View\Tabber
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/Returns/order/style.css';

        return $list;
    }
}
