<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SearchPanel\Profile\Admin;

/**
 * Main admin profile search panel
 */
class CustomerProfiles extends \XLite\View\SearchPanel\Profile\Admin\Main
{
    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        $conditions = parent::defineConditions();

        $conditions['user_type'][static::CONDITION_CLASS] = 'XLite\View\FormField\Select\CheckboxList\CustomerUserType';
        // Allow some modules like multi-vendor not to add vendor-related statuses
        $conditions['status'][static::CONDITION_CLASS] = 'XLite\View\FormField\Select\AccountStatusSearchCustomer';

        return $conditions;
    }
}
