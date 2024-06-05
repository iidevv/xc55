<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated widget displaying the invoice.
 * @Extender\Mixin
 */
class Invoice extends \XLite\View\Invoice
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/LoyaltyProgram/order/invoice/parts/earned_points.css';

        return $list;
    }

    /**
     * Check whether the invoice should include information on the number of reward
     * points it gives to the shopper.
     *
     * @return boolean
     */
    public function isRewardPointsPromoVisible()
    {
        $order = $this->getOrder();

        return $order
            && $this->getCalculatedRewardPoints()
            && $order->isUserInLoyaltyProgram()
            && $this->isRewardGivingStatus($order);
    }

    /**
     * Returns the promo text that appears in invoices and informs about
     * the number of reward points the user will earn for the order.
     *
     * @return string
     */
    protected function getRewardPointsPromoText()
    {
        return $this->t(
            'Congratulations! With this order you earn X reward points.',
            [
                'points' => $this->getCalculatedRewardPoints(),
            ]
        );
    }

    /**
     * Returns the number of reward points that the user will earn for the order.
     *
     * @return int
     */
    protected function getCalculatedRewardPoints()
    {
        return $this->getOrder()->getCalculatedRewardPoints();
    }

    /**
     * Check whether the order has the status that gives the shopper rewards.
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function isRewardGivingStatus(\XLite\Model\Order $order)
    {
        return in_array($order->getPaymentStatus()->getCode(), $this->getRewardGivingStatuses());
    }

    /**
     * Get an array of order statuses which give the shopper rewards.
     *
     * @return array
     */
    protected function getRewardGivingStatuses()
    {
        return [
            \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
            \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED,
            \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
            \XLite\Model\Order\Status\Payment::STATUS_PAID,
        ];
    }
}
