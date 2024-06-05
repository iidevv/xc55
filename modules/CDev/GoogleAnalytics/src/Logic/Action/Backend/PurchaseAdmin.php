<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action\Backend;

use XLite\Model\Order;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\Action\Interfaces\IBackendAction;

/**
 * Class PurchaseAdmin
 */
class PurchaseAdmin extends Action\Base\ABackendAction implements IBackendAction
{
    /**
     * @var Order|\CDev\GoogleAnalytics\Model\Order
     */
    protected $order;

    /**
     * @var array
     */
    protected $itemsPurchased;

    public function __construct(Order $order, array $itemsPurchased)
    {
        $this->order          = $order;
        $this->itemsPurchased = $itemsPurchased;
    }

    public static function getEventCategory(): string
    {
        return 'Checkout';
    }

    public static function getEventActionName(): string
    {
        return 'Purchase';
    }

    public static function getEventAction(): string
    {
        return 'purchase';
    }

    public function isBackendApplicable(): bool
    {
        return parent::isBackendApplicable()
            && $this->order;
    }

    public function getClientId(): string
    {
        return $this->order->getGaClientId() ?: parent::getClientId();
    }

    protected function buildRequestData(): array
    {
        $products = [];
        if ($this->itemsPurchased) {
            $counter = 1;
            foreach ($this->itemsPurchased as $itemPurchase) {
                $products[] = GA::getOrderItemDataMapper()->getDataForBackend($itemPurchase['item'], abs($itemPurchase['change']), $counter++);
            }
        }

        return GA::getOrderDataMapper()->getChangeDataForBackend($this->order, [], $products);
    }
}
