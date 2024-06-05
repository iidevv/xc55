<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Module\CDev\SalesTax\Model\Tax;

use XCart\Extender\Mapping\Extender;

/**
 * Rate
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SalesTax")
 */
class Rate extends \CDev\SalesTax\Model\Tax\Rate
{
    protected function getItemBasis($item)
    {
        $result = parent::getItemBasis($item);

        $formulaParts = explode('+', $this->getTaxableBaseType());

        if (
            in_array('SH', $formulaParts, true)
            && $this->isIgnoreProductsWithFixedFee()
        ) {
            $result += $item->getObject()
                ? $item->getObject()->getFreightFixedFee() * $item->getAmount()
                : 0;
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isIgnoreProductsWithFixedFee()
    {
        return \XLite\Core\Config::getInstance()->XC->FreeShipping->freight_shipping_calc_mode
            === \XC\FreeShipping\View\FormField\FreightMode::FREIGHT_ONLY;
    }
}
