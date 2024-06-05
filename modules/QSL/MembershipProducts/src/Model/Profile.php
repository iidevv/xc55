<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Returns true if profile has opened/active assigned memberships
     *
     * @return boolean
     */
    public function hasOpenedAssignedMemberships()
    {
        /** @var \QSL\MembershipProducts\Model\Repo\OrderItem $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\OrderItem');

        return count($repo->findItemsWithOpenedAppliedMemberships($this)) > 0;
    }

    public function mergeWithProfile(\XLite\Model\Profile $profile, $flag = self::MERGE_ALL)
    {
        if (
            $profile->getAnonymous()
            && $profile->getMembership()
        ) {
            $this->setMembership($profile->getMembership());
        }

        return parent::mergeWithProfile($profile, $flag);
    }
}
