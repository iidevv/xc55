<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Coupon model form extension
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Coupons")
 */
class Coupon extends \CDev\Coupons\View\Model\Coupon
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionDefault()
    {
        $this->schemaDefault['value'][self::SCHEMA_DEPENDENCY] = [
            self::DEPENDENCY_HIDE => [
                'type' => [\CDev\Coupons\Model\Coupon::TYPE_FREESHIP],
            ],
        ];

        return $this->getFieldsBySchema($this->schemaDefault);
    }
}
