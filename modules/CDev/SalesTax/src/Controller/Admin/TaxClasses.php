<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class TaxClasses extends \XLite\Controller\Admin\TaxClasses
{
    /**
     * Check - is current place enabled or not
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return true;
    }
}
