<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActTodaysDeal\View\FormField\Select;

class Category extends \XLite\View\FormField\Select\Category
{
    protected function getOptions()
    {
        $list = ['no_category' => static::t('No category assigned')];
        $list += parent::getOptions();

        return $list;
    }
}