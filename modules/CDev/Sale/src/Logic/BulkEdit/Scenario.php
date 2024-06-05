<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic\BulkEdit;

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
        $result['product_price_and_membership']['fields']['default']['participate_sale'] = [
            'class'    => 'CDev\Sale\Logic\BulkEdit\Field\Product\ParticipateSale',
            'options' => [
                'position' => 170,
            ],
        ];

        $result['product_price_and_membership']['fields']['default']['sale_price'] = [
            'class'    => 'CDev\Sale\Logic\BulkEdit\Field\Product\SalePrice',
            'options' => [
                'position' => 171,
            ],
        ];

        return $result;
    }
}
