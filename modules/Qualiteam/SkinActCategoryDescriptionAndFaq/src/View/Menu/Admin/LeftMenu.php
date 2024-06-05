<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (isset($items['catalog'][static::ITEM_CHILDREN])) {
            $items['catalog'][static::ITEM_CHILDREN]['category_questions'] = [
                static::ITEM_TITLE  => static::t('SkinActCategoryDescriptionAndFaq Category Questions'),
                static::ITEM_TARGET => 'category_questions',
                static::ITEM_WEIGHT => 250,
            ];
        }

        return $items;
    }
}
