<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action;

use XLite;
use XLite\Controller\Customer\CheckoutSuccess;
use XLite\Model\Order;
use CDev\GoogleAnalytics\Core\GA;

class CheckoutComplete extends Base\AAction
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
        return 'checkout_complete';
    }

    public function isApplicable(): bool
    {
        return parent::isApplicable()
            && GA::getResource()->isECommerceEnabled()
            && XLite::getController() instanceof CheckoutSuccess
            && $this->order instanceof Order;
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

        return [
            'products'   => $productsData,
            'actionData' => (object) [],
        ];
    }
}
