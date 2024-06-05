<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * Login form in popup
 * @Extender\Mixin
 */
class PopupLogin extends \XLite\View\Button\PopupLogin
{
    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target' => 'login',
            'widget' => '\XLite\View\Authorization',
            'fromURL' => $this->getFromURL(),
        ];
    }
}
