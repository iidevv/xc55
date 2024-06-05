<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\FreeShipping\View\Product\Details;

use XCart\Extender\Mapping\Extender;

/**
 * Product details 'Free shipping' label widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\FreeShipping")
 */
class Label extends \XC\FreeShipping\View\Product\Details\Label
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'labels/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
