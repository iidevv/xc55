<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Cart
 * @Extender\Mixin
 */
class Cart extends \XLite\Model\Cart
{
    /**
     * Check - AvaTax forbid checkout or not
     *
     * @return boolean
     */
    public function isAvaTaxForbidCheckout()
    {
        $result =  $this->getAvaTaxErrorsFlag()
            && \XLite\Core\Config::getInstance()->XC->AvaTax->require_taxes_for_order;

        if ($result) {
            $modifier = $this->getModifier(
                \XLite\Model\Base\Surcharge::TYPE_TAX,
                \XC\AvaTax\Logic\Order\Modifier\StateTax::MODIFIER_CODE
            );
            $result = $modifier->canApply();
        }

        return $result;
    }

    /**
     * Define fingerprint keys
     *
     * @return array
     */
    protected function defineFingerprintKeys()
    {
        return array_merge(
            parent::defineFingerprintKeys(),
            ['avaTaxErrorsFlag']
        );
    }

    /**
     * Get fingerprint by 'avaTaxErrorsFlag' key
     *
     * @return array
     */
    protected function getFingerprintByAvaTaxErrorsFlag()
    {
        return $this->getAvaTaxErrorsFlag();
    }
}
