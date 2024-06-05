<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\XC\News\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\News")
 */
class TopNewsSideBar extends \XC\News\View\TopNewsSideBar
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'modules/XC/News/side_bar_box/style.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
