<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/Sale/css/lc.less';

        return $list;
    }
}
