<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\Logic\BulkEdit\Field\Product;

class LowStockLimit extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'             => static::t('Low limit quantity'),
                'type'              => 'XLite\View\FormModel\Type\PatternType',
                'inputmask_pattern' => [
                    'alias'      => 'integer',
                    'rightAlign' => false,
                ],
                'position'          => $options['position'] ?? 0,
            ],
        ];
    }

    public static function getData($name, $object)
    {
        return [
            $name => 0,
        ];
    }

    public static function populateData($name, $object, $data)
    {
        $object->setLowLimitAmount($data->{$name});
    }

    /**
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    public static function getViewColumns($name, $options)
    {
        return [
            $name => [
                'name'    => static::t('Low limit quantity'),
                'orderBy' => $options['position'] ?? 0,
            ],
        ];
    }

    /**
     * @param $name
     * @param $object
     *
     * @return array
     */
    public static function getViewValue($name, $object)
    {
        return $object->getInventoryEnabled()
            ? $object->getLowLimitAmount()
            : '';
    }
}
