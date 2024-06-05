<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\Logic\BulkEdit\Field\Product;

class Weight extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'             => static::t('Weight'),
                'type'              => 'XLite\View\FormModel\Type\SymbolType',
                'symbol'            => \XLite\Core\Config::getInstance()->Units->weight_symbol,
                'inputmask_pattern' => [
                    'alias'          => 'xcdecimal',
                    'digitsOptional' => false,
                    'rightAlign'     => false,
                    'digits'         => 4,
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
        $object->setWeight($data->{$name});
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
                'name'    => static::t('Weight'),
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
        return \XLite\View\AView::formatWeight($object->getWeight());
    }
}
