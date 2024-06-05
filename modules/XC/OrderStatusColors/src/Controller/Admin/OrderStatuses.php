<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XC\OrderStatusColors\Model\OrderStatusColor;
use XLite\Model\Order\Status\Payment;
use XLite\Model\Order\Status\Shipping;
use XC\OrderStatusColors\View\ItemsList\Model\Order\Status\Colors;

/**
 * @Extender\Mixin
 */
abstract class OrderStatuses extends \XC\CustomOrderStatuses\Controller\Admin\OrderStatuses
{
    /**
     * Return items list class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        if ($this->getPage() === 'order_status_colors') {
            return Colors::class;
        }

        return parent::getItemsListClass();
    }

    /**
     * Do update colors action
     */
    protected function doActionUpdateColors()
    {
        $colors = Request::getInstance()->colors;

        $repo = Database::getRepo(OrderStatusColor::class);

        foreach ($colors as $payment_status_id => $data) {
            $paymentStatus = $this->getPaymentStatus($payment_status_id);
            foreach ($data as $shipping_status_id => $colorCode) {
                $shippingStatus = $this->getSippingStatus($shipping_status_id);
                $color          = $repo->findOneBy([
                    'paymentStatus'  => $paymentStatus,
                    'shippingStatus' => $shippingStatus,
                ]);
                if (!$color) {
                    $color = new OrderStatusColor();
                    $color->setPaymentStatus($paymentStatus);
                    $color->setShippingStatus($shippingStatus);
                }
                $color->setColor($colorCode);
                $color->update();
            }
        }

        TopMessage::addInfo('Data have been saved successfully');
    }

    /**
     * Return payment status
     *
     * @param integer $id
     *
     * @return Payment
     */
    protected function getPaymentStatus($id)
    {
        return Database::getRepo(Payment::class)
            ->find($id);
    }

    /**
     * Return shipping status
     *
     * @param integer $id
     *
     * @return Shipping
     */
    protected function getSippingStatus($id)
    {
        return Database::getRepo(Shipping::class)
            ->find($id);
    }
}
