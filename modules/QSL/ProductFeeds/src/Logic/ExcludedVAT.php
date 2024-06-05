<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Logic;

use XCart\Extender\Mapping\Extender;

/**
 * Apply the ExcludedVAT modifier on VAT prices.
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\VAT")
 */
class ExcludedVAT extends \CDev\VAT\Logic\ExcludedVAT
{
    public static function isApply(\XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        // VAT price is calculated on top of the display price that may already
        // include VAT (so we don't add VAT once again if it does)
        return ($purpose === 'vat')
            ? !\XLite\Core\Config::getInstance()->CDev->VAT->display_prices_including_vat
            : parent::isApply($model, $property, $behaviors, $purpose);
    }
}
