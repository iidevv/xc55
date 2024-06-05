<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\Order\Admin;

use XLite\View\FormField\Input\SearchByAddressRadio;

/**
 * Main admin orders list search panel
 */
class Main extends \XLite\View\SearchPanel\Order\Admin\AAdmin
{
    /**
     * @var \XLite\View\ItemsList\Model\Table
     */
    protected $itemsList;

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'search_panel/order/style.css';

        return $list;
    }

    /**
     * Get form class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\XLite\View\Form\Order\Search';
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.orders';
    }

    /**
     * Get itemsList
     *
     * @return \XLite\View\ItemsList\Model\Table
     */
    protected function getItemsList()
    {
        if (!$this->itemsList) {
            $this->itemsList = parent::getItemsList()
                ?: new \XLite\View\ItemsList\Model\Order\Admin\Search();
        }

        return $this->itemsList;
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
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY         => true,
                \XLite\View\FormField\Input\Text::PARAM_PLACEHOLDER => static::t('OrderID or email, ID1-ID2 for range'),
            ],
            'paymentStatus' => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Select\CheckboxList\OrderStatus\Payment',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY          => true,
            ],
            'shippingStatus' => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Select\CheckboxList\OrderStatus\Shipping',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY          => true,
            ],
            'dateRange' => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
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
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text\Profile',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Customer'),
                \XLite\View\FormField\Input\AInput::PARAM_PLACEHOLDER => static::t('Customer name'),
                \XLite\View\FormField\Input\Text\Profile::PARAM_PROFILE_ID => $this->getCondition('profileId'),
                \XLite\View\FormField\Input\Text\Profile::PARAM_AUTOCOMPLETE => true,
            ],
            'accessLevel' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Select\Order\CustomerAccessLevel',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Access level'),
            ],
            'address' => [
                static::CONDITION_CLASS => SearchByAddressRadio::class,
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Search by address'),
                \XLite\View\FormField\Input\Radio::PARAM_VALUE => $this->getCondition('address') ?: 'both',
            ],
            'country'     => [
                static::CONDITION_CLASS                                        => 'XLite\View\FormField\Select\Country',
                \XLite\View\FormField\AFormField::PARAM_LABEL                  => static::t('Country'),
                \XLite\View\FormField\AFormField::PARAM_VALUE                  => $this->getCondition('country') ?: '',
                \XLite\View\FormField\Select\Country::PARAM_STATE_SELECTOR_ID  => 'stateSelectorId',
                \XLite\View\FormField\Select\Country::PARAM_STATE_INPUT_ID     => 'stateBoxId',
                \XLite\View\FormField\Select\Country::PARAM_DENY_SINGLE_OPTION => true,
            ],
            'state'       => [
                static::CONDITION_CLASS                              => 'XLite\View\FormField\Select\State',
                \XLite\View\FormField\AFormField::PARAM_LABEL        => static::t('State'),
                \XLite\View\FormField\AFormField::PARAM_ID           => 'stateSelectorId',
                \XLite\View\FormField\Select\State::PARAM_SELECT_ONE => true,
            ],
            'customState' => [
                static::CONDITION_CLASS                       => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('State'),
                \XLite\View\FormField\AFormField::PARAM_ID    => 'stateBoxId',
            ],
            'city' => [
                static::CONDITION_CLASS                       => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('City'),
            ],
            'zipcode' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Zip/postal code'),
            ],
            'recent' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Checkbox',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Awaiting processing'),
                \XLite\View\FormField\Input\Checkbox::PARAM_IS_CHECKED => $this->getCondition('recent'),
                \XLite\View\FormField\Input\Checkbox::PARAM_VALUE => '1',
            ],
            'transactionID' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('Payment ID'),
            ],
            'sku' => [
                static::CONDITION_CLASS => 'XLite\View\FormField\Input\Text',
                \XLite\View\FormField\AFormField::PARAM_LABEL => static::t('SKU'),
                \XLite\View\FormField\Input\AInput::PARAM_PLACEHOLDER => static::t('SKU or SKU1, SKU2'),
            ],
        ];
    }

    /**
     * Return true if search panel should use filters
     *
     * @return boolean
     */
    protected function isUseFilter()
    {
        return true;
    }

    /**
     * Get name of the 'Reset filter' option
     *
     * @return string
     */
    protected function getClearFilterName()
    {
        return static::t('All orders');
    }

    /**
     * Define search filters options
     * TODO: Review and correct before commit!
     *
     * @return array
     */
    protected function defineFilterOptions()
    {
        $result = parent::defineFilterOptions();

        // Calculate recent orders number
        $count = \XLite\Core\Database::getRepo('XLite\Model\Order')->searchRecentOrders(null, true);

        if ($count) {
            $recentOrdersFilter = new \XLite\Model\SearchFilter();
            $recentOrdersFilter->setId('recent');
            $recentOrdersFilter->setName(static::t('Awaiting processing'));
            $recentOrdersFilter->setSuffix(sprintf('(%d)', $count));
            $result = ['recent' => $recentOrdersFilter] + $result;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getHiddenConditions()
    {
        $conditions = parent::getHiddenConditions();

        // Add empty cnd for correct display conditions
        $conditions[] = [];

        return $conditions;
    }
}
