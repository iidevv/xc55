<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\SearchPanel\ProductsReturn\Admin;

/**
 * Main admin orders list search panel
 */
class Main extends \XC\CanadaPost\View\SearchPanel\ProductsReturn\Admin\AAdmin
{
    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CanadaPost/search_panel/return/style.css';

        return $list;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XC\CanadaPost\View\Form\ProductsReturn\Search';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.capost-returns-list';
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
            'substring' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Enter Return # or Order #'),
            ],
            'status' => [
                static::CONDITION_CLASS => '\XC\CanadaPost\View\FormField\Select\ReturnStatus',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XC\CanadaPost\View\FormField\Select\ReturnStatus::PARAM_ALL_OPTION  => true,
            ],
            'dateRange' => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ],
        ];
    }
}
