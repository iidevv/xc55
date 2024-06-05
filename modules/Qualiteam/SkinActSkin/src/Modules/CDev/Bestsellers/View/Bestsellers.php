<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\CDev\Bestsellers\View;

use XCart\Extender\Mapping\Extender;

/**
 * Bestsellers widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Bestsellers")
 */
class Bestsellers extends \CDev\Bestsellers\View\Bestsellers
{

    /**
     * Return link URL
     *
     * @return string
     */
    protected function getDialogLink() : string
    {
        return $this->buildURL('bestsellers');
    }

}
