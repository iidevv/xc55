<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract customer
 * @Extender\Mixin
 */
class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get fingerprint difference
     *
     * @param array $old Old fingerprint
     * @param array $new New fingerprint
     *
     * @return array
     */
    protected function getCartFingerprintDifference(array $old, array $new)
    {
        $diff = parent::getCartFingerprintDifference($old, $new);

        if (
            isset($old['avaTaxErrorsFlag'])
            && isset($new['avaTaxErrorsFlag'])
            && $old['avaTaxErrorsFlag'] != $new['avaTaxErrorsFlag']
        ) {
            $diff['avaTaxErrorsFlag'] = $new['avaTaxErrorsFlag'];
        }

        return $diff;
    }
}
