<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Order\Status\Payment;
use QSL\MembershipProducts\Logic\MembershipProducts;

/**
 * Class represents an order
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    protected function getStatusHandlers($oldStatus, $newStatus, $type)
    {
        $handlers = parent::getStatusHandlers($oldStatus, $newStatus, $type);

        if ($this->isEligibleForMembershipProductsHandlers($oldStatus, $newStatus, $type)) {
            $oldCode = $oldStatus->getCode();
            $newCode = $newStatus->getCode();

            $membershipProductsHandlers = $this->getMembershipProductsStatusHandlers();

            if ($oldCode && $newCode && isset($membershipProductsHandlers[$oldCode][$newCode])) {
                $handlers = array_merge($handlers, $membershipProductsHandlers[$oldCode][$newCode]);
            }
        }

        return $handlers;
    }

    /**
     * Check if the entity is eligble for executing Membership Products status handlers.
     *
     * @param mixed  $oldStatus Old order status
     * @param mixed  $newStatus New order status
     * @param string $type      Type
     *
     * @return boolean
     */
    protected function isEligibleForMembershipProductsHandlers($oldStatus, $newStatus, $type)
    {
        return ($type === 'payment');
    }

    /**
     * Return base part of the certain "change status" handler name
     *
     * @return string|array
     */
    protected function getMembershipProductsStatusHandlers()
    {
        return [
            Payment::STATUS_QUEUED     => [
                Payment::STATUS_PAID     => ['applyMembership'],
                Payment::STATUS_DECLINED => [],
                Payment::STATUS_CANCELED => [],
            ],
            Payment::STATUS_DECLINED   => [
                Payment::STATUS_AUTHORIZED => [],
                Payment::STATUS_PART_PAID  => [],
                Payment::STATUS_PAID       => ['applyMembership', ''],
                Payment::STATUS_QUEUED     => [],
            ],
            Payment::STATUS_CANCELED   => [
                Payment::STATUS_AUTHORIZED => [],
                Payment::STATUS_PART_PAID  => [],
                Payment::STATUS_PAID       => ['applyMembership'],
                Payment::STATUS_QUEUED     => [],
            ],
            Payment::STATUS_REFUNDED   => [
                Payment::STATUS_DECLINED => [],
                Payment::STATUS_CANCELED => [],
                Payment::STATUS_PAID     => [],
            ],
            Payment::STATUS_AUTHORIZED => [
                Payment::STATUS_DECLINED => [],
                Payment::STATUS_CANCELED => [],
                Payment::STATUS_PAID     => ['applyMembership'],
            ],
            Payment::STATUS_PART_PAID  => [
                Payment::STATUS_DECLINED => [],
                Payment::STATUS_CANCELED => [],
                Payment::STATUS_PAID     => ['applyMembership'],
            ],
            Payment::STATUS_PAID       => [
                Payment::STATUS_QUEUED     => ['cancelApplyMembership'],
                Payment::STATUS_DECLINED   => ['cancelApplyMembership'],
                Payment::STATUS_CANCELED   => ['cancelApplyMembership'],
                Payment::STATUS_REFUNDED   => ['cancelApplyMembership'],
                Payment::STATUS_AUTHORIZED => ['cancelApplyMembership'],
                Payment::STATUS_PART_PAID  => ['cancelApplyMembership'],
            ],
        ];
    }

    /**
     * Status change handler for the "applyMembership" event.
     */
    public function processApplyMembership()
    {
        // Since only registered customers can have a membership,
        // we can skip anonymous processing completely
        if (
            $origProfile = $this->getOrigProfile()
            || (
                $origProfile
                && !$origProfile->getAnonymous()
            )
        ) {
            MembershipProducts::getInstance()->applyMembershipProduct($this);
        }
    }

    /**
     * Status change handler for the "cancelApplyMembership" event.
     */
    public function processCancelApplyMembership()
    {
        // Since only registered customers can have a membership,
        // we can skip anonymous processing completely
        if (
            $origProfile = $this->getOrigProfile()
            || (
                $origProfile
                && !$origProfile->getAnonymous()
            )
        ) {
            MembershipProducts::getInstance()->cancelApplyMembershipProduct($this);
        }
    }
}
