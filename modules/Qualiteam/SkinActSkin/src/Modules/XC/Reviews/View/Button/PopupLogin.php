<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\XC\Reviews\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * Login form in popup
 *
 * @Extender\Mixin
 */
class PopupLogin extends \XC\Reviews\View\Button\PopupLogin
{

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' btn-lg');
    }

}
