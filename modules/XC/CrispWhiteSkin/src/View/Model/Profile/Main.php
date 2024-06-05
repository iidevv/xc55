<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * \XLite\View\Model\Profile\Main
 *
 * @Extender\Mixin
 */
class Main extends \XLite\View\Model\Profile\Main
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = [
            'file'  => 'css/less/signin.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
