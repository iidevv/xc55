<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\Panel;

use XCart\Extender\Mapping\ListChild;

/**
 * Coupons panel
 *
 * @ListChild (list="coupons.itemsList.footer", zone="admin")
 */
class Coupons extends \XLite\View\Base\StickyPanel
{
    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Coupons/coupons/panel';
    }
}
