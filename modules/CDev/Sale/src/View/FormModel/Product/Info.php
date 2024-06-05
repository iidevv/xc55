<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
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
        $list[] = [
            'file'  => 'modules/CDev/Sale/form_model/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $saleDiscounts = [];
        foreach (\XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')->findAllProductSpecific() as $saleDiscount) {
            /** @var \CDev\Sale\Model\SaleDiscount $saleDiscount */
            $saleDiscounts[$saleDiscount->getId()] = $saleDiscount->getName();
        }

        $schema = static::compose(
            $schema,
            [
                'prices_and_inventory' => [
                    'price' => [
                        'participate_sale' => [
                            'label'            => static::t('Sale'),
                            'show_label_block' => false,
                            'type'             => 'Symfony\Component\Form\Extension\Core\Type\CheckboxType',
                            'position'         => 300,
                        ],
                        'sale_price'       => [
                            'type'             => 'CDev\Sale\View\FormModel\Type\Sale',
                            'show_label_block' => false,
                            'show_when'        => [
                                '..' => [
                                    'participate_sale' => true,
                                ],
                            ],
                            'position'         => 400,
                        ],
                    ],
                ],
            ]
        );

        $schema['prices_and_inventory']['group_discounts'] =    [
            'label'    => static::t('Global discounts'),
            'type'     => 'XLite\View\FormModel\Type\Select2Type',
            'multiple' => true,
            'choices'  => array_flip($saleDiscounts),
            'position' => 320,
            'show_when'        => [
                'prices_and_inventory' => [
                    'price' => [
                        'participate_sale' => false,
                    ]
                ],
            ],
        ];

        return $schema;
    }
}
