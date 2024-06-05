<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\SearchPanel\Admin;

use QSL\AbandonedCartReminder\View\ItemsList\Table\RecoveredOrder as ItemsList;

/**
 * Search panel widget for the Cart Recovery Statistics page.
 */
class CartRecoveryStats extends \XLite\View\SearchPanel\ASearchPanel
{
    /**
     * Via this method the widget registers the CSS files which it uses.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/AbandonedCartReminder/search_panel/cart_recovery_stats.css';

        return $list;
    }

    /**
     * Get form class.
     *
     * @return string
     */
    protected function getFormClass()
    {
        return '\QSL\AbandonedCartReminder\View\Form\Search\CartRecoveryStats';
    }

    /**
     * Define conditions.
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [
            ItemsList::PARAM_DATE_RANGE => [
                static::CONDITION_CLASS => '\XLite\View\FormField\Input\Text\DateRange',
                \XLite\View\FormField\AFormField::PARAM_FIELD_ONLY  => true,
            ],
        ];
    }
}
