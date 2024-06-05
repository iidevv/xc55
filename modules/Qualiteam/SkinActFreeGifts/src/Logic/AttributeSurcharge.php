<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Logic;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AttributeSurcharge extends \XLite\Logic\AttributeSurcharge
{
    public static function modifyMoney($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        $checkGift = false;

        if ($model instanceof \XLite\Model\OrderItem) {
            $checkGift = $model->getFreeGift();
        }

        return $checkGift ? $value : parent::modifyMoney($value, $model, $property, $behaviors, $purpose);
    }
}
