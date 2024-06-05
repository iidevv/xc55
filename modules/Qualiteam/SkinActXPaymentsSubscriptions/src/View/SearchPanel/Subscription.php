<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\SearchPanel;

use Qualiteam\SkinActXPaymentsSubscriptions\View\FormField\Select\SubscriptionStatus as FormFieldSubscriptionStatus;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\AInput;
use XLite\View\FormField\Input\Text;
use XLite\View\FormField\Input\Text\DateRange;
use XLite\View\SearchPanel\ASearchPanel;

/**
 * Main admin product search panel
 */
class Subscription extends ASearchPanel
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return \Qualiteam\SkinActXPaymentsSubscriptions\View\Form\Search\Subscription::class;
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.subscription';
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
                'id'          => [
                    static::CONDITION_CLASS      => Text::class,
                    AInput::PARAM_PLACEHOLDER    => static::t('Order or Subscription ID'),
                    AFormField::PARAM_FIELD_ONLY => true,
                ],
                'productName' => [
                    static::CONDITION_CLASS      => Text::class,
                    AInput::PARAM_PLACEHOLDER    => static::t('Product name'),
                    AFormField::PARAM_FIELD_ONLY => true,
                ],
                'status'      => [
                    static::CONDITION_CLASS                                    => FormFieldSubscriptionStatus::class,
                    FormFieldSubscriptionStatus::PARAM_DISPLAY_SEARCH_STATUSES => true,
                    AFormField::PARAM_FIELD_ONLY                               => true,
                ],
            ];
    }

    /**
     * Define hidden conditions
     *
     * @return array
     */
    protected function defineHiddenConditions()
    {
        return parent::defineHiddenConditions() + [
                'dateRange'     => [
                    static::CONDITION_CLASS => DateRange::class,
                    AFormField::PARAM_LABEL => static::t('Date of purchase'),
                ],
                'nextDateRange' => [
                    static::CONDITION_CLASS => DateRange::class,
                    AFormField::PARAM_LABEL => static::t('Date of the next payment'),
                ],
            ];
    }
}
