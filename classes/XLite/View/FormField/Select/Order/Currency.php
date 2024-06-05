<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Order;

/**
 * Memberships selector
 */
class Currency extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get Memberships list
     *
     * @return array
     */
    protected function getCurrencies()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Currency')->findUsed();
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $result = [];

        foreach ($this->getCurrencies() as $currency) {
            $label = $currency->getCode();
            if ($currency->getCurrencySymbol()) {
                $label .= sprintf(' (%s)', $currency->getCurrencySymbol());
            }

            $result[$currency->getCurrencyId()] = $label;
        }

        return $result;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return 1 < count($this->getCurrencies());
    }
}
