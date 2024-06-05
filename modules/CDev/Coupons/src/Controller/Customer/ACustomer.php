<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer
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
            count($old['coupons']) !== count($new['coupons'])
            || count($old['coupons']) !== count(array_intersect($old['coupons'], $new['coupons']))
        ) {
            $diff['coupons'] = [];
            foreach (array_diff($new['coupons'], $old['coupons']) as $id) {
                $diff['coupons'][] = [
                    'id'    => $id,
                    'state' => 'added',
                ];
            }

            foreach (array_diff($old['coupons'], $new['coupons']) as $id) {
                $diff['coupons'][] = [
                    'id'    => $id,
                    'state' => 'removed',
                ];
            }
        }

        if (!empty($diff['coupons']) && empty($diff['total'])) {
            $diff['total'] = $new['total'];
        }

        return $diff;
    }
}
