<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\View\FormField\Select;

/**
 * Membership selector widget
 */
class SelectMemberships extends \XLite\View\FormField\Select\Memberships
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
        return [0 => static::t('No membership')] + $this->getMembershipsList();
    }
}
