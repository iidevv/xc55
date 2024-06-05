<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Class Info
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/FreeShipping/form_model/product_info.less';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $currency = \XLite::getInstance()->getCurrency();
        $currencySymbol = $currency->getCurrencySymbol(false);

        $schema['shipping']['fixed_shipping_freight'] = [
            'label'             => static::t('Freight'),
            'help'              => static::t('This field can be used to set a fixed shipping fee for the product. Make sure the field value is a positive number (greater than zero).'),
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
            'show_when'         => [
                'shipping' => [
                    'requires_shipping_status' => true,
                    'requires_shipping' => [
                        'free_shipping' => false,
                        'ship_for_free' => false,
                    ],
                ],
            ],
            'position'          => 400,
        ];

        return $this->defineFieldsFreeShipping($schema);
    }

    protected function defineFieldsFreeShipping($schema)
    {
        $schema = static::compose(
            $schema,
            [
                'shipping' => [
                    'requires_shipping' => [
                        'ship_for_free'          => [
                            'label'            => static::t('Free shipping'),
                            'show_label_block' => false,
                            'type'             => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'position'         => 200,
                        ],
                        'free_shipping'          => [
                            'label'            => static::t('Exclude from shipping cost calculation'),
                            'show_label_block' => false,
                            'type'             => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'position'         => 300,
                        ],
                    ],
                ],
            ]
        );

        return $schema;
    }
}
