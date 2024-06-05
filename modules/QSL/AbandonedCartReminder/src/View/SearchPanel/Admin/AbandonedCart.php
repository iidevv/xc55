<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\SearchPanel\Admin;

/**
 * Search Panel widget for Abandoned Carts page.
 */
class AbandonedCart extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * Widget param names
     */
    public const PARAM_SUBSTRING  = 'email';
    public const PARAM_DATE_RANGE = 'dateRange';

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\ItemsList\AItemsListSearch';
    }

    /**
     * Define conditions.
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
            static::PARAM_SUBSTRING => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Customer email'),
            ],
            static::PARAM_DATE_RANGE => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ],
        ];
    }
}
