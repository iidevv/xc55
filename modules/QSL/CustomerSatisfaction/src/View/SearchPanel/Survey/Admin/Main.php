<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\SearchPanel\Survey\Admin;

/**
 * Main admin reviews list search panel
 *
 */
class Main extends \QSL\CustomerSatisfaction\View\SearchPanel\Survey\Admin\AAdmin
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

        $list[] = 'modules/QSL/CustomerSatisfaction/search_panel/survey/style.css';

        return $list;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\QSL\CustomerSatisfaction\View\Form\SurveysSearch';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.surveys';
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
/*            'orderId' => array(
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY         => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Order #'),
            ),*/
            'keywords' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY         => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Order # or customer info'),
            ],
            'rating' => [
                static::CONDITION_CLASS => '\QSL\CustomerSatisfaction\View\FormField\Select\SurveyRating',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY          => true,
                \XLite\View\FormField\Select\OrderStatus\Shipping::PARAM_ALL_OPTION  => true,
            ],
            'status' => [
                static::CONDITION_CLASS => '\QSL\CustomerSatisfaction\View\FormField\Select\SurveyStatuses',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY          => true,
            ],
            'dateRange' => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ],
        ];
    }
}
