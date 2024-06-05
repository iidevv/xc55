<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Module\CDev\SalesTax\View;

use XCart\Extender\Mapping\Extender;

/**
 * Taxes widget (admin)
 * 
 * @Extender\Mixin
 * @Extender\Depend("CDev\SalesTax")
 */
class Taxes extends \CDev\SalesTax\View\Taxes
{
    /**
     * @return string
     */
    protected function getOptionFieldsTemplate()
    {
        return 'modules/Qualiteam/SkinActQuickbooks/sales_tax/options.twig';
    }
    
    public function getQuickbooksTaxName()
    {
        $tax = $this->getTax();
        
        if ($tax) {
            return $tax->getQbTaxName();
        }
        
        return '';
    }
}