<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View\ItemsList\Model;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\View\ItemsList\Model\Category
{

    protected function defineColumns()
    {
        $cols = parent::defineColumns();

        $cols['showInMobileApp'] = [
            static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActGraphQLApi showInMobileApp'),
            static::COLUMN_CLASS => '\XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff',
            static::COLUMN_ORDERBY => 210,
        ];

        return $cols;
    }
}