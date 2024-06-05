<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActXPaymentsSubscriptions\Logic\ExcludedVAT;
use Qualiteam\SkinActXPaymentsSubscriptions\Logic\IncludedVAT;
use XCart\Extender\Mapping\Extender;
use XLite\Model\AEntity;

/**
 * Money modificator
 *
 * @Extender\Mixin
 */
class MoneyModificator extends \XLite\Model\MoneyModificator
{
    /**
     * Apply
     *
     * @param float                $value     Property value
     * @param AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return float
     */
    public function apply($value, AEntity $model, $property, array $behaviors, $purpose)
    {
        $xpSubscriptionsModifiers = [
            IncludedVAT::class,
            ExcludedVAT::class,
        ];

        if (
            !Manager::getRegistry()->isModuleEnabled('CDev\VAT')
            && in_array($this->getClass(), $xpSubscriptionsModifiers)
        ) {
            $result = $value;
        } else {
            $result = parent::apply($value, $model, $property, $behaviors, $purpose);
        }

        return $result;
    }
}
