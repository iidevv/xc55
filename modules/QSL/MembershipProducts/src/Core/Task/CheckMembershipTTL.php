<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Core\Task;

/**
 * Check membership TTL
 */
class CheckMembershipTTL extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Check membership TTL');
    }

    /**
     * Run step
     */
    protected function runStep()
    {
        /** @var \QSL\MembershipProducts\Model\Repo\OrderItem $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\OrderItem');

        $repo->checkAndRevertAssignedMemberships();
        $repo->checkAndAssignUnassignedMemberships();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return static::INT_1_DAY;
    }
}
