<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\Logic\BulkEdit\Field\Product;

class InventoryTrackingStatus extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'    => static::t('Inventory tracking for this product is'),
                'type'     => 'XLite\View\FormModel\Type\SwitcherType',
                'position' => $options['position'] ?? 0,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => true,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $object->setInventoryEnabled($data->{$name});
    }
}
