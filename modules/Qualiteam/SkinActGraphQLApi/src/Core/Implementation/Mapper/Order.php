<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XcartGraphqlApi\DTO\OrderDTO;

/**
 * Class Cart
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper
 */
class Order
{
    /**
     * @param \XLite\Model\Order $order
     * @return OrderDTO
     */
    public function mapToDto(\XLite\Model\Order $order)
    {
        $dto = new OrderDTO();

        $dto->orderId = $order->getOrderId();
        $dto->orderNumber = $order->getOrderNumber();
        $dto->total = $order->getTotal();
        $dto->subtotal = $order->getSubtotal();

        $shippingCost = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);
        $dto->shippingCost = $shippingCost;

        $dto->paymentFee = 0;
        $dto->taxAmount = $this->calculateTaxAmount($order);
        $dto->discountValue = $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT);
        $dto->currency = $order->getCurrency()->getCode();

        $dto->orderDate = \XLite\Core\Converter::formatDate($order->getDate());
        $dto->orderTime = \XLite\Core\Converter::formatDayTime($order->getDate());
        $dto->marketplaceId = $order->getMarketplaceId();

        $dto->updateDate = \XLite\Core\Converter::formatTime($order->getLastRenewDate());
        $dto->trackingNumber = $order->getTrackingNumbers()->first() ? $order->getTrackingNumbers()->first()->getValue() : '';
        $dto->customerNotes  = $order->getNotes();
        $dto->adminNotes     = $order->getAdminNotes();
        $dto->user           = $order->getOrigProfile();

        $dto->paymentMethod  = $this->getPaymentMethodName($order);
        $dto->paymentStatus  = $order->getPaymentStatusCode();
        $dto->paymentStatusStr  = $order->getPaymentStatus() ? $order->getPaymentStatus()->getName() : '';


        $dto->shippingMethod = $order->getShippingMethodName();
        $dto->shippingStatus = $order->getShippingStatusCode();
        $dto->shippingStatusStr = $order->getShippingStatus() ? $order->getShippingStatus()->getName() : '';
        $dto->shippingStatusBar = $this->getShippingStatusesBar($order);

        $profile = $order->getProfile() ?: $order->getOrigProfile();
        $dto->shippingInfo   = $profile->getShippingAddress();

        $dto->items = $order->getItems();

        $dto->cartModel = $order;

        $dto->adminOrderUri = \XLite\Core\Converter::buildFullURL('order', '', [
            'order_number' => $order->getOrderNumber(),
            'vendor_product_editor' => 1,
        ], \XLite::getAdminScript());

        $dto->customerOrderUri = \XLite\Core\Converter::buildFullURL('order', '', [
            'order_number' => $order->getOrderNumber(),
        ]);

        $dto->unreadMessages = $order->countUnreadMessages();

        if ($order->getDeliveredDate() > 0) {
            $dto->deliveredDate = \XLite\Core\Converter::formatTime($order->getDeliveredDate());
        }

        $trackit = new \Qualiteam\SkinActMain\Helper\Trackit($order);
        $dto->trackingUrls = $trackit->getTrackingUrls();

        return $dto;
    }

    /**
     * @param $model
     * @return mixed
     */
    protected function getPaymentMethodName($model)
    {
        $paymentMethodName = $model->getPaymentMethodName();
        if (!$paymentMethodName) {
            $t = $model->getPaymentTransactions()->first();
            if ($t) {
                $paymentMethodName = $t->getPaymentMethod()
                    ? $t->getPaymentMethod()->getTitle()
                    : $t->getMethodLocalName();
            }
        }

        return $paymentMethodName;
    }

    protected function getAvailableStatusesForShippingBar()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order\Status\Shipping')->getShippingBarStatuses();
    }

    protected function getShippingStatusesBar(\XLite\Model\Order $order)
    {
        $statuses = [];
        if (Container::getContainer()->get(ModuleManagerDomain::class)->isEnabled('XC-CustomOrderStatuses')) {
            $available_statuses = $this->getAvailableStatusesForShippingBar();
            foreach ($available_statuses as $k => $status) {
                $status['is_checked'] = false;
                $statuses[] = $status;
            }

            $order_status_name = $order->getShippingStatus()->getName();

            foreach ($statuses as $k => &$status) {
                $status['is_checked'] = true;
                if ($status['name'] === $order_status_name) {
                    break;
                }
            }
        }

        return $statuses;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected function calculateTaxAmount(\XLite\Model\Order $order)
    {
        $surcharges = $order->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_TAX);
        $total = 0;

        foreach ($surcharges as $s) {
            $total += $s->getValue();
        }

        return round($total, 2);
    }

    /**
     * @param \XLite\Model\Order $order
     * @param                   $value
     *
     * @return float
     */
    protected function roundPriceForCart(\XLite\Model\Order $order, $value)
    {
        return $order->getCurrency()
            ? $order->getCurrency()->roundValue($value)
            : $value;
    }
}
