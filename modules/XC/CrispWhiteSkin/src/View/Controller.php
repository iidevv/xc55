<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Controller main widget
 * @Extender\Mixin
 */
class Controller extends \XLite\View\Controller
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        if (!$this->isLogged() || $this->isForceChangePassword()) {
            $list[] = [
                'file'  => 'css/less/signin.less',
                'merge' => 'bootstrap/css/bootstrap.less',
            ];
        }

        return $list;
    }
}
