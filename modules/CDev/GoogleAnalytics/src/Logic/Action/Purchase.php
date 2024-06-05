<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action;

use XLite;
use XLite\Controller\Customer\CheckoutSuccess;
use XLite\Core\Session;
use XLite\Model\Order;
use CDev\GoogleAnalytics\Core\GA;

class Purchase extends Base\AAction
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * Action constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    protected static function getActionType(): string
    {
        return 'purchase';
    }

    public function isApplicable(): bool
    {
        return parent::isApplicable()
            && GA::getResource()->isECommerceEnabled()
            && GA::getResource()->isPurchaseImmediatelyOnSuccess()
            && XLite::getController() instanceof CheckoutSuccess
            && $this->order instanceof Order
            && !$this->isOrderProcessed();
    }

    protected function isOrderProcessed(): bool
    {
        $orders = Session::getInstance()->gaProcessedOrders;

        if (!is_array($orders)) {
            $orders = [];
        }

        return !$this->order
            || !$this->order->getProfile()
            || in_array($this->order->getOrderId(), $orders, true);
    }

    protected function buildRequestData(): array
    {
        $productsData = [];

        foreach ($this->order->getItems() as $item) {
            if (!$item->getObject()) {
                continue;
            }

            $productsData[] = GA::getOrderItemDataMapper()->getData($item);
        }

        $data = [];

        if ($productsData) {
            $data = [
                'products'   => $productsData,
                'actionData' => GA::getOrderDataMapper()->getPurchaseData($this->order),
            ];
        }

        if (($data['actionData'] ?? null)) {
            $this->markOrderAsProcessed();
        }

        return $data;
    }

    protected function markOrderAsProcessed(): void
    {
        $orders = Session::getInstance()->gaProcessedOrders;

        if (!is_array($orders)) {
            $orders = [];
        }

        $orders[] = $this->order->getOrderId();


        Session::getInstance()->gaProcessedOrders = $orders;
    }
}
