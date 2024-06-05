<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\MarketPrice\Logic\BulkEdit;

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
        $result['product_price_and_membership']['fields']['default']['market_price'] = [
            'class'    => 'CDev\MarketPrice\Logic\BulkEdit\Field\Product\MarketPrice',
            'options' => [
                'position' => 150,
            ],
        ];

        return $result;
    }
}
