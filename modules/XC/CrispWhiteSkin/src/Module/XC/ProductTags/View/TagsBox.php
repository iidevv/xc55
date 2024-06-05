<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\ProductTags\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductTags")
 */
class TagsBox extends \XC\ProductTags\View\TagsBox
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'file'  => 'modules/XC/ProductTags/tags_box/css/style.less',
                'merge' => 'bootstrap/css/bootstrap.less'
            ]
        );
    }
}
