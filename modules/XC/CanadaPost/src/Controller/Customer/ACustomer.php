<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract customer
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get fingerprint difference
     *
     * @params array $old Old fingerprint
     * @params array $new New fingerprint
     *
     * @return array
     */
    protected function getCartFingerprintDifference(array $old, array $new)
    {
        $diff = parent::getCartFingerprintDifference($old, $new);

        $cellKeys = [
            'capostOfficeId',
            'capostShippingZipCode',
        ];

        foreach ($cellKeys as $name) {
            if ($old[$name] != $new[$name]) {
                $diff[$name] = $new[$name];
            }
        }

        return $diff;
    }
}
