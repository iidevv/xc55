<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Logic;

/**
 * Class handling common Membership Products function.
 */
class MembershipProducts extends \XLite\Base
{
    /**
     * Apply membership product
     *
     * @return boolean
     */
    public function applyMembershipProduct(\XLite\Model\Order $order)
    {
        $result = false;

        /** @var \QSL\MembershipProducts\Model\OrderItem $item */
        foreach ($order->getItems() as $item) {
            if ($item->canApplyMembershipToCustomer()) {
                $result = $item->applyMembershipToCustomer();
                break;
            }
        }

        return $result;
    }

    /**
     * Cancel apply membership product
     *
     * @return boolean
     */
    public function cancelApplyMembershipProduct(\XLite\Model\Order $order)
    {
        $result = false;

        /** @var \QSL\MembershipProducts\Model\OrderItem $item */
        foreach ($order->getItems() as $item) {
            if ($item->resetCustomerMembership()) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
