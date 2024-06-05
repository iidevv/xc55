<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * \XLite\View\FormField\Select\Membership
 */
class Membership extends \XLite\View\FormField\Select\Regular
{
    /**
     * Determines if this field is visible for customers or not
     *
     * @var boolean
     */
    protected $isAllowedForCustomer = true;


    /**
     * Get Memberships list
     *
     * @return array
     */
    protected function getMembershipsList()
    {
        $list = [];
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Membership')->findActiveMemberships() as $m) {
            $list[$m->membership_id] = $m->getName();
        }

        return $list;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return ['' => static::t('Ignore membership')]
            + $this->getMembershipsList();
    }
}
