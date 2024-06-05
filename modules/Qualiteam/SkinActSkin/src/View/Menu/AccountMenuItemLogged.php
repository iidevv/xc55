<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkin\View\Menu;

use XLite\Core\Auth;

class AccountMenuItemLogged extends AccountMenuItem
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && Auth::getInstance()->isLogged();
    }
}
