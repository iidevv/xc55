<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\FormField\Select;

/**
 * Widget for 'Who can leave feedback' selector
 *
 */
class WhoCanLeaveFeedback extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get users groups list
     *
     * @return array
     */
    protected function getUsersGroupsList()
    {
        return [
            \XC\Reviews\Model\Review::REGISTERED_CUSTOMERS => static::t('Registered users only'),
            \XC\Reviews\Model\Review::PURCHASED_CUSTOMERS  => static::t('Registered users who purchased product'),
        ];
    }

    /**
     * Get list of default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getUsersGroupsList();
    }
}
