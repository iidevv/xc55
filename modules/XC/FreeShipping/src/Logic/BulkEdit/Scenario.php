<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Logic\BulkEdit;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\BulkEditing")
 */
class Scenario extends \XC\BulkEditing\Logic\BulkEdit\Scenario
{
    /**
     * @return array
     */
    protected static function defineScenario()
    {
        $result = parent::defineScenario();
        $result['product_shipping_info']['fields']['default']['ship_for_free'] = [
            'class'    => 'XC\FreeShipping\Logic\BulkEdit\Field\Product\ShipForFree',
            'options' => [
                'position' => 210,
            ],
        ];
        $result['product_shipping_info']['fields']['default']['free_shipping'] = [
            'class'    => 'XC\FreeShipping\Logic\BulkEdit\Field\Product\FreeShipping',
            'options' => [
                'position' => 220,
            ],
        ];
        $result['product_shipping_info']['fields']['default']['freight_fixed_fee'] = [
            'class'    => 'XC\FreeShipping\Logic\BulkEdit\Field\Product\FreightFixedFee',
            'options' => [
                'position' => 230,
            ],
        ];

        return $result;
    }
}
