<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View\FormModel\Product;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class InfoSale extends \XLite\View\FormModel\Product\Info
{
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $applyToWholesaleField = [
            'label'     => static::t('Apply product-specific discount to wholesale price'),
            'type'      => 'XLite\View\FormModel\Type\SwitcherType',
            'position'  => 300,
            'help'      => static::t('This option will apply if the "Sale" parameter is set as a percentage.'),
            'show_when' => [
                'prices_and_inventory' => [
                    'price' => [
                        'sale_price' => [
                            'type' => 'sale_percent',
                        ],
                        'participate_sale' => true,
                    ]
                ],
            ],
        ];

        if (Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants')) {
            $applyToWholesaleField['help'] = static::t('This option also affects product variants. It will apply if the "Sale" parameter is set as a percentage.');
            $applyToWholesaleField['show_when'] = [];
        }

        $schema['prices_and_inventory']['applySaleToWholesale'] = $applyToWholesaleField;

        return $schema;
    }
}
