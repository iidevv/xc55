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
class Main extends \XLite\Controller\Admin\Main
{
    /**
     * Return 'Taxes' url
     *
     * @return string
     */
    public function getTaxesURL()
    {
        return $this->buildURL('sales_tax');
    }
}
