<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Factory\Commands;

use XLite\Model\Order as OrderModel;
use Qualiteam\SkinActYotpoReviews\Core\Command\Create\Order;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Post\Create;

class CreateOrderCommandFactory
{
    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Orders\Post\Create $container
     */
    public function __construct(
        private Create $container
    ) {
    }

    public function createCommand(OrderModel $order): Order
    {
        return new Order($this->container, $order);
    }
}
