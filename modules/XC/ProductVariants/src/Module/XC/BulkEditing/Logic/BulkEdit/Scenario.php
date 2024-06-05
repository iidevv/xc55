<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\XC\BulkEditing\Logic\BulkEdit;

use XCart\Extender\Mapping\Extender;

/**
 * Scenario
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\BulkEditing")
 */
class Scenario extends \XC\BulkEditing\Logic\BulkEdit\Scenario
{
    protected static function defineScenario()
    {
        return array_merge_recursive(parent::defineScenario(), [
            'product_inventory' => [
                'fields' => [
                    'default' => [
                        'clear_variants_inventory' => [
                            'class'   => 'XC\ProductVariants\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product\VariantsTrackingStatus',
                            'options' => [
                                'position' => 150,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
