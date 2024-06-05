<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Account status selector widget for 'Search users' search panel
 */
class AccountStatusSearch extends \XLite\View\FormField\Select\AccountStatus
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            '' => static::t('Any status'),
        ] + parent::getDefaultOptions();
    }
}
