<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\Logic\BulkEdit\Field\Product;

class Price extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    public static function getSchema($name, $options)
    {
        $currency = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        return [
            $name => [
                'label'             => static::t('Price'),
                'type'              => 'XLite\View\FormModel\Type\SymbolType',
                'symbol'            => $currencySymbol,
                'inputmask_pattern' => [
                    'alias'      => 'xcdecimal',
                    'prefix'     => '',
                    'rightAlign' => false,
                    'digits'     => $currency->getE(),
                ],
                'constraints'       => [
                    'Symfony\Component\Validator\Constraints\GreaterThanOrEqual' => [
                        'value'   => 0,
                        'message' => static::t('Minimum value is X', ['value' => 0]),
                    ],
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
        $object->setPrice($data->{$name});
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
                'name'    => static::t('Price'),
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
        return \XLite\View\AView::formatPrice($object->getPrice());
    }
}
