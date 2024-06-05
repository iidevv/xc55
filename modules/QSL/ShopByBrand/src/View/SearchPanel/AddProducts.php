<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\SearchPanel;

use XLite\View\SearchPanel\ProductSelections\Admin\Main;

/**
 * Add products popup search panel class
 */
class AddProducts extends Main
{
    /**
     * Defines the form class.
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'QSL\ShopByBrand\View\Form\Brand\AddProducts';
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
}
