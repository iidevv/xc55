<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\SearchPanel\Admin;

/**
 * Search panel widget for the Cart Recovery Statistics page.
 */
class CartEmailStats extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * Widget param names
     */
    public const PARAM_DATE_RANGE = 'dateRange';

    /**
     * Via this method the widget registers the CSS files which it uses.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/QSL/AbandonedCartReminder/search_panel/email_stats.css',
            ]
        );
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\QSL\AbandonedCartReminder\View\Form\Search\CartEmailStats';
    }

    /**
     * Define conditions.
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
            static::PARAM_DATE_RANGE => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ],
        ];
    }
}
