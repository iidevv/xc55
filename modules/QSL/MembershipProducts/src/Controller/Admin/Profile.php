<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Something customer can put into his cart
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Admin\Profile
{
    /**
     * Register anonymous profile
     */
    protected function doActionRegisterAsNew()
    {
        parent::doActionRegisterAsNew();

        $profile = $this->getModelForm()->getModelObject();

        if (!$profile->getAnonymous()) {
            $this->applyMembershipProductMembershipToCustomer($profile);
        }
    }

    /**
     * Merge anonymous profile with registered
     */
    protected function doActionMergeWithRegistered()
    {
        $profile = $this->getModelForm()->getModelObject();

        if (
            $profile
            && $profile->isPersistent()
            && $profile->getAnonymous()
            && !$profile->getOrder()
        ) {
            $same = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findUserWithSameLogin($profile);
            if ($same && !$same->isAdmin()) {
                $this->applyMembershipProductMembershipToCustomer($same);
            }
        }

        parent::doActionMergeWithRegistered();
    }

    /**
     * Apply membership product membership to customer
     *
     * @param \XLite\Model\Profile $profile
     */
    protected function applyMembershipProductMembershipToCustomer(\XLite\Model\Profile $profile = null)
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\OrderItem');

        foreach ($repo->findItemsWithOpenedAppliedMemberships($profile) as $item) {
            if ($item->canApplyMembershipToCustomer()) {
                $item->applyMembershipToCustomer();

                \XLite\Core\Database::getEM()->flush($item);
            }
        }
    }
}
