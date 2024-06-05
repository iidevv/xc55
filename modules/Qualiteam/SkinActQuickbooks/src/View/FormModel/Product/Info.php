<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product form model
 * 
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $fields = parent::defineFields();
        
        $fields[self::SECTION_DEFAULT]['quickbooks_fullname'] = [
            'label'       => static::t('QuickBooks Item Name/Number'),
            'constraints' => [
                'XLite\Core\Validator\Constraints\MaxLength' => [
                    'length'  => 255,
                ],
            ],
            'position'    => 210,
        ];
        
        return $fields;
    }
}