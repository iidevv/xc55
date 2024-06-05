<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Put CDev\VolumeDiscounts\Logic\Order\Modifier\Discount discount before all others discounts
     *
     * @param array $modifiers Modifiers
     *
     * @return array
     */
    protected function postprocessSurchargeTotals(array $modifiers)
    {
        $modifiers = parent::postprocessSurchargeTotals($modifiers);

        $currentVolumeDiscountWeight = 0;
        // found min weight for the group of discounts
        foreach ($modifiers as $code => $modifier) {
            if ($modifier['object']->getType() === \XLite\Model\Base\Surcharge::TYPE_DISCOUNT) {
                if ($this->isVolumeDiscountSurcharge($modifier['object'])) {
                    $foundModifierDiscountCode = $code;
                    $currentVolumeDiscountWeight = $modifier['object']->getSortingWeight();
                } else {
                    $minGroupWeight = min($minGroupWeight ?? 0, $modifier['object']->getSortingWeight());
                }
            }
        }

        if (
            !empty($foundModifierDiscountCode)
            && isset($minGroupWeight)
            && $currentVolumeDiscountWeight >= $minGroupWeight
        ) {
            // put \CDev\VolumeDiscounts\Logic\Order\Modifier\Discount to the first position as it distributesDiscounts twice in AOM unlike other discount
            $modifiers[$foundModifierDiscountCode]['object']->getOwner()->getModifier(
                \XLite\Model\Base\Surcharge::TYPE_DISCOUNT,
                \CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE
            )->setSortingWeight($currentVolumeDiscountWeight - 1);

            uasort($modifiers, static function ($a, $b) {
                $aWeight = $a['object']
                    ? $a['object']->getSortingWeight()
                    : 0;
                $bWeight = $b['object']
                    ? $b['object']->getSortingWeight()
                    : 0;
                return $aWeight < $bWeight ? -1 : $aWeight > $bWeight;
            });
        }

        return $modifiers;
    }

    /**
     * Return true if code is DISCOUNT surcharge code
     *
     * @param \XLite\Model\Order\Surcharge $surcharge Surcharge
     *
     * @return boolean
     */
    protected function isVolumeDiscountSurcharge($surcharge)
    {
        $discountModifier = $surcharge->getOwner()->getModifier(
            \XLite\Model\Base\Surcharge::TYPE_DISCOUNT,
            \CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE
        );

        return $discountModifier->isSurchargeOwner($surcharge);
    }

    /**
     * Assemble volume discount dump surcharge
     *
     * @return array
     */
    protected function assembleDiscountDumpSurcharge()
    {
        return $this->assembleDefaultDumpSurcharge(
            \XLite\Model\Base\Surcharge::TYPE_DISCOUNT,
            \CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE,
            '\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount',
            static::t('Discount')
        );
    }

    /**
     * Get required surcharges
     *
     * @return array
     */
    protected function getRequiredSurcharges()
    {
        return array_merge(
            parent::getRequiredSurcharges(),
            [\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE]
        );
    }

    /**
     * Add human readable name for DISCOUNT modifier code
     *
     * @return array
     */
    protected static function getFieldHumanReadableNames()
    {
        return array_merge(
            parent::getFieldHumanReadableNames(),
            [
                \CDev\VolumeDiscounts\Logic\Order\Modifier\Discount::MODIFIER_CODE  => 'Discount',
            ]
        );
    }
}
