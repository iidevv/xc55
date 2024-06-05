<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\View;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Checkout extends \XLite\View\Checkout
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActKlarna/checkout/style.less';

        return $list;
    }
}