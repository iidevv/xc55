<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View;

use XC\MultiCurrency\Core\MultiCurrency;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiCurrency")
 */
class MultiCurrencyController extends Controller
{
    protected function getCloudSearchDynamicPricesEnabledCacheKey(): array
    {
        $key = parent::getCloudSearchDynamicPricesEnabledCacheKey();

        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        $key[] = $selectedCurrency->getCode();

        return $key;
    }

    /**
     * Enable dynamic prices if store is not on a default currency
     */
    protected function isCloudSearchDynamicPricesEnabled(): bool
    {
        $mainCurrency = \XLite::getInstance()->getCurrency();

        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        return $mainCurrency->getCurrencyId() !== $selectedCurrency->getCurrency()->getCurrencyId()
            || parent::isCloudSearchDynamicPricesEnabled();
    }
}
