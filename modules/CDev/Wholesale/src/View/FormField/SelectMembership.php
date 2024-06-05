<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View\FormField;

/**
 * Membership selector widget
 */
class SelectMembership extends \XLite\View\FormField\Select\Membership
{
    /**
     * shortName
     *
     * @var   string
     */
    protected $shortName = 'membership';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return ['' => static::t('All customers wholesale')] + $this->getMembershipsList();
    }
}
