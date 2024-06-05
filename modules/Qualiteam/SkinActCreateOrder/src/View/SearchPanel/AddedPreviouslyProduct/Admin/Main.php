<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\SearchPanel\AddedPreviouslyProduct\Admin;

class Main extends \XLite\View\SearchPanel\ProductSelections\Admin\Main
{
    protected function isUseFilter()
    {
        return false;
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.added_previously_product';
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\Form\AddedPreviouslyProduct\Search';
    }
}