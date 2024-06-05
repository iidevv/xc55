<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
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

        $schema[self::SECTION_DEFAULT]['tags'] = [
            'label'      => static::t('Tags'),
            'type'       => 'XC\ProductTags\View\FormModel\Type\TagsType',
            'multiple'   => true,
            'position'   => 650,
        ];

        return $schema;
    }
}
