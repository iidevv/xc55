<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Core;

/**
 * Some methods for operations over prices
 */
abstract class Price extends \XLite\View\AView
{
    /**
     * Custom precision to display numbers
     *
     * @param       $price
     * @param null  $currency
     * @param false $strictFormat
     *
     * @return string
     */
    public static function longFormat($price, $currency = null, $strictFormat = false)
    {
        if ($currency === null) {
            $currency = \XLite::getInstance()->getCurrency();
        }

        $currencyE = $currency->getE();

        $rounded   = round($price - floor($price), 4);
        $requiredE = strlen($rounded) - 2;

        if ($requiredE > $currencyE) {
            $currency->setE($requiredE);
            $res = static::formatPrice($price, $currency, $strictFormat);
            $currency->setE($currencyE);

            return $res;
        }

        return static::formatPrice($price, $currency, $strictFormat);
    }
}
