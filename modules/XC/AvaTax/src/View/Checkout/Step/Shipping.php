<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\View\Checkout\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Shipping step
 * @Extender\Mixin
 */
class Shipping extends \XLite\View\Checkout\Step\Shipping
{
    /**
     * Check - step is complete or not
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return parent::isCompleted()
            && !$this->getCart()->isAvaTaxForbidCheckout();
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/AvaTax/checkout.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/AvaTax/checkout.less';

        return $list;
    }

    /**
     * Check - AvaTax address verification is enabled or not
     *
     * @return boolean
     */
    protected function isAvaTaxAddressVerificationEnabled()
    {
        return \XC\AvaTax\Core\TaxCore::getInstance()->isValid()
            && \XLite\Core\Config::getInstance()->XC->AvaTax->addressverif;
    }
}
