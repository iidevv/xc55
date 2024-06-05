<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\ItemsList\Model\Shipping\Popup;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping custom rates
 * @Extender\Mixin
 */
class Offline extends \XLite\View\ItemsList\Model\Shipping\Popup\Offline
{
    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowRemoveButton(\XLite\Model\Shipping\Method $method)
    {
        return parent::isShowRemoveButton($method)
            && !$method->getFree()
            && !$method->isFixedFee();
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowHandlingFee(\XLite\Model\Shipping\Method $method)
    {
        return parent::isShowHandlingFee($method)
            && !$method->getFree()
            && !$method->isFixedFee();
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowTaxClass(\XLite\Model\Shipping\Method $method)
    {
        return parent::isShowTaxClass($method)
            && !$method->getFree()
            && !$method->isFixedFee();
    }
}
