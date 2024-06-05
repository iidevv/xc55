<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $schema['prices_and_inventory']['ava_tax_code'] = [
            'label'       => static::t('Tax code (AvaTax)'),
            'constraints' => [
                'XLite\Core\Validator\Constraints\MaxLength' => [
                    'length'  => 25,
                ],
            ],
            'position'    => 250,
        ];

        return $schema;
    }
}
