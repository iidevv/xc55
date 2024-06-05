<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action\Base;

use XLite\Model\OrderItem;
use XLite\Model\Product;
use CDev\GoogleAnalytics\Core\GA;
use CDev\GoogleAnalytics\Logic\Action;

abstract class AOrderItem extends Action\Base\AAction
{
    /**
     * @var OrderItem
     */
    protected $item;

    /**
     * Action constructor.
     *
     * @param OrderItem $item
     */
    public function __construct(OrderItem $item)
    {
        $this->item = $item;
    }

    public function isApplicable(): bool
    {
        return parent::isApplicable()
            && GA::getResource()->isECommerceEnabled()
            && $this->item instanceof OrderItem
            && $this->item->getObject() instanceof Product;
    }

    protected function buildRequestData(): array
    {
        return GA::getOrderItemDataMapper()->getData(
            $this->item
        );
    }
}
