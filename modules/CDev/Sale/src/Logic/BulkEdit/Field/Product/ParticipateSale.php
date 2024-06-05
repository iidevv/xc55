<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic\BulkEdit\Field\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Depend ("XC\BulkEditing")
 */
class ParticipateSale extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'            => static::t('On sale'),
                'type'             => 'XLite\View\FormModel\Type\SwitcherType',
                'position'         => $options['position'] ?? 0,
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
        $object->setParticipateSale($data->{$name});
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
                'name'    => static::t('On sale'),
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
        return $object->getParticipateSale() ? static::t('Yes') : static::t('No');
    }
}
