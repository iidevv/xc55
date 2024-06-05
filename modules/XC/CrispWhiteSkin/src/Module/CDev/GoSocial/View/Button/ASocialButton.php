<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Module\CDev\GoSocial\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ASocialButton extends \CDev\GoSocial\View\Button\ASocialButton
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
            'file'  => 'modules/CDev/GoSocial/likely.less',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }
}
