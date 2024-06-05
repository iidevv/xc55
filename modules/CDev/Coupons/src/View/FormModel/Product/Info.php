<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $coupons = [];
        foreach (\XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon')->findAllProductSpecific() as $coupon) {
            /** @var \CDev\Coupons\Model\Coupon $coupon */
            $coupons[$coupon->getId()] = $coupon->getCode();
        }

        $schema['prices_and_inventory']['coupons'] = [
            'label'    => static::t('Coupons'),
            'type'     => 'XLite\View\FormModel\Type\Select2Type',
            'multiple' => true,
            'choices'  => array_flip($coupons),
            'position' => 150,
        ];

        return $schema;
    }
}
