<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Subcategories list
 * @Extender\Mixin
 */
class Subcategories extends \XLite\View\Subcategories
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (!\XLite::isAdminZone()) {
            $list[] = 'subcategories/subcategories.js';
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/subcategories.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
