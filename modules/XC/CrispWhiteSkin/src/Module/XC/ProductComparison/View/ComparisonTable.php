<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\ProductComparison\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductComparison")
 */
class ComparisonTable extends \XC\ProductComparison\View\ComparisonTable
{
    /**
     * @return string[]
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'file'  => 'modules/XC/ProductComparison/css/style.less',
                'merge' => 'bootstrap/css/bootstrap.less'
            ]
        );
    }
}
