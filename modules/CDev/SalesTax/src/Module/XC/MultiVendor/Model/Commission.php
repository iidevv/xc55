<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\Module\XC\MultiVendor\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Base\Surcharge;
use CDev\SalesTax\Logic\Order\Modifier\Tax as SalesTax;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class Commission extends \XC\MultiVendor\Model\Commission
{
    protected function getCommissionSalesTaxValue()
    {
        $result = parent::getCommissionSalesTaxValue();
        $order = $this->getOrder();
        $surcharges = $order->getSurchargesByType(Surcharge::TYPE_TAX);

        foreach ($surcharges as $surcharge) {
            /* @var \XLite\Model\Order\Surcharge $surcharge */
            if ($this->canApplySalesTax($surcharge)) {
                $result += $order->getCurrency()->roundValue($surcharge->getValue());
            }
        }

        return $result;
    }

    protected function canApplySalesTax(\XLite\Model\Order\Surcharge $surcharge)
    {
        return $surcharge->getModifier()
            && $surcharge->getModifier()->getModifier()->getCode() === SalesTax::MODIFIER_CODE
            && $surcharge->getAvailable()
            && !$surcharge->getInclude(); //not included in case of customization
    }
}
