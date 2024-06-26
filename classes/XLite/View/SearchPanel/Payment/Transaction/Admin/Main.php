<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\Payment\Transaction\Admin;

/**
 * Main admin payment transaction search panel
 */
class Main extends \XLite\View\SearchPanel\Payment\Transaction\Admin\AAdmin
{
    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\ItemsList\Payment\Transaction\Search';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.payment-transactions';
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
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Order number or email'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ],
            'public_id' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('Public ID'),
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ],
            'date' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
            ],
            'status' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Select\PaymentTransactionStatus',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY => true,
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
            'customerName' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Customer name'),
            ],
            'zipcode' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Customer zip/postal code'),
            ],
        ];
    }
}
