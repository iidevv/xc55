<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\QSL\MyWishlist\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 * @Extender\After("QSL\MyWishlist")
 */
abstract class Header extends \XLite\View\Header
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $key = array_search('modules/QSL/MyWishlist/style.css', $list);
        if ($key !== false) {
            unset($list[$key]);
        }

        $list[] = [
            'file'  => 'modules/QSL/MyWishlist/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/MyWishlist/header/header_widget.js';

        return $list;
    }
}
