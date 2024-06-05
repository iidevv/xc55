<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\SearchPanel;

/**
 * Main admin orders list search panel
 */
class Main extends \XLite\View\SearchPanel\ProductSelections\Admin\Main
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\Qualiteam\SkinActFreeGifts\View\Form\ItemsList\ProductSelection\Search';
    }

    /**
     * Prepare the value of the condition
     *
     * @param array $condition
     *
     * @return mixed
     */
    protected function prepareConditionValue($condition)
    {
        return $condition[static::CONDITION_CELL] === 'substring' ? '' : parent::prepareConditionValue($condition);
    }

//    /**
//     * Define conditions
//     *
//     * @return array
//     */
//    protected function defineConditions()
//    {
//        $conditions = parent::defineConditions();
//
//        $conditions['categoryId'] = [
//            static::CONDITION_CLASS => 'XLite\View\FormField\Select\Select2\Category',
//            \XLite\View\FormField\Select\Category::PARAM_DISPLAY_NO_CATEGORY => true,
//            \XLite\View\FormField\Select\Category::PARAM_DISPLAY_ROOT_CATEGORY => false,
//            \XLite\View\FormField\Select\Category::PARAM_DISPLAY_ANY_CATEGORY => true,
//            \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
//        ];
//
//        return $conditions;
//    }
}
