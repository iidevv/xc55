<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View\CloudFilters;


use XC\MultiCurrency\Core\MultiCurrency;
use XCart\Extender\Mapping\Extender;

/**
 * Cloud filters sidebar box widget
 *
 * @Extender\Mixin
 * @Extender\Depend ({"XC\MultiCurrency"})
 */
class FiltersBoxMultiCurrency extends \QSL\CloudSearch\View\CloudFilters\FiltersBox
{
    /**
     * Get commented widget data
     *
     * @return array
     */
    protected function getPhpToJsData()
    {
        $data = parent::getPhpToJsData();

        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        $data['currencyFormat']['rate'] = $selectedCurrency ? $selectedCurrency->getRate() : 1;

        return $data;
    }

    /**
     * Get current currency
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        return $selectedCurrency ? $selectedCurrency->getCurrency() : parent::getCurrency();
    }
}
