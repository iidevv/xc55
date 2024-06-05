<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\XC\BulkEditing\Logic\BulkEdit\Field\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Scenario
 *
 * @Extender\Depend("XC\BulkEditing")
 */
class VariantsTrackingStatus extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'     => static::t('Clear and disable inventory tracking for variants as well'),
                'type'      => 'XLite\View\FormModel\Type\SwitcherType',
                'position'  => $options['position'] ?? 0,
                'show_when' => [
                    'default' => [
                        'inventory_tracking_status' => false,
                    ],
                ],
                'help'      => static::t('Product variants inventory clear help'),
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => false,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        if ($data->{$name}) {
            foreach ($object->getVariants() as $variant) {
                $variant->setDefaultAmount(true);
            }
        }
    }
}
