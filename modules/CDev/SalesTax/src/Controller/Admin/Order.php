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
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Assemble shipping dump surcharge
     *
     * @return array
     */
    protected function assembleCdevstaxDumpSurcharge()
    {
        $code = \CDev\SalesTax\Logic\Order\Modifier\Tax::getSurchargeCode();

        return $code
            ? $this->assembleDefaultDumpSurcharge(
                \XLite\Model\Base\Surcharge::TYPE_TAX,
                $code,
                '\CDev\SalesTax\Logic\Order\Modifier\Tax',
                static::t('Sales tax')
            )
            : null;
    }

    /**
     * Get required surcharges
     *
     * @return array
     */
    protected function getRequiredSurcharges()
    {
        $result = parent::getRequiredSurcharges();

        if (\CDev\SalesTax\Logic\Order\Modifier\Tax::getSurchargeCode()) {
            $result = array_merge(
                $result,
                [\CDev\SalesTax\Logic\Order\Modifier\Tax::MODIFIER_CODE]
            );
        }

        return $result;
    }

    /**
     * Postprocess surcharge totals
     *
     * @param array $modifiers Modifiers
     *
     * @return array
     */
    protected function postprocessSurchargeTotals(array $modifiers)
    {
        $modifiers = parent::postprocessSurchargeTotals($modifiers);

        // Search for sales taxes surcharges and leave only one (first) surcharge

        $first = null;

        foreach ($modifiers as $code => $modifier) {
            if ($this->isSalesTaxSurcharge($modifier['object'])) {
                if (!$first) {
                    $first = $modifier;
                } else {
                    unset($modifiers[$code]);
                }
            }
        }

        return $modifiers;
    }

    /**
     * Return true if code is SalesTax surcharge code
     *
     * @param \XLite\Model\Order\Surcharge $surcharge Surcharge
     *
     * @return boolean
     */
    protected function isSalesTaxSurcharge($surcharge)
    {
        $salesTaxModifier = $surcharge->getOwner()->getModifier(
            \XLite\Model\Base\Surcharge::TYPE_TAX,
            \CDev\SalesTax\Logic\Order\Modifier\Tax::MODIFIER_CODE
        );

        return $salesTaxModifier->isSurchargeOwner($surcharge);
    }

    /**
     * Add human readable name for CDEV.STAX modifier code
     *
     * @return array
     */
    protected static function getFieldHumanReadableNames()
    {
        $result = parent::getFieldHumanReadableNames();

        $code = \CDev\SalesTax\Logic\Order\Modifier\Tax::getSurchargeCode();

        if ($code) {
            $result = array_merge($result, [$code => 'Sales tax']);
        }

        return $result;
    }
}
