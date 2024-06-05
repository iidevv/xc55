<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product form model
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return array
     */
    protected function defineFields()
    {
        $schema = parent::defineFields();
        $schema['marketing']['facebookMarketingEnabled'] = [
            'label'      => static::t('Facebook product feed enabled'),
            'type'       => 'XLite\View\FormModel\Type\SwitcherType',
            'position'   => 650,
            'show_when' => [
                static::SECTION_DEFAULT => [
                    'available_for_sale' => '1',
                ],
            ],
        ];

        return $schema;
    }
}
