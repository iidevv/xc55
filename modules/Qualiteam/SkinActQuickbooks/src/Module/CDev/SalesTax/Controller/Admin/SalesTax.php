<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Module\CDev\SalesTax\Controller\Admin;

use XLite\Core\Request;
use XCart\Extender\Mapping\Extender;

/**
 * Taxes controller
 * 
 * @Extender\Mixin
 * @Extender\Depend("CDev\SalesTax")
 */
class SalesTax extends \CDev\SalesTax\Controller\Admin\SalesTax
{
    /**
     * Update tax rate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $qbTaxName = substr(Request::getInstance()->qb_tax_name, 0, 31);
        
        $tax = $this->getTax();
        
        $tax->setQbTaxName($qbTaxName);
        
        parent::doActionUpdate();
    }
}