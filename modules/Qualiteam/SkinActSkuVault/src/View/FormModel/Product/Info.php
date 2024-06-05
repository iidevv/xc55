<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $schema['prices_and_inventory']['skip_sync_to_skuvault'] = [
            'label'    => static::t('Skip sync to SkuVault'),
            'type'     => 'XLite\View\FormModel\Type\SwitcherType',
            'position' => 500,
        ];

        return $schema;
    }
}
