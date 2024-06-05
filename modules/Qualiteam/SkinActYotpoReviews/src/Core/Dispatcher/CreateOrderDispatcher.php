<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Dispatcher;

use Qualiteam\SkinActYotpoReviews\Core\Factory\Commands\CreateOrderCommandFactory;
use Qualiteam\SkinActYotpoReviews\Messenger\Message\ExportMessage;
use XCart\Container;
use XLite\Model\Order;

class CreateOrderDispatcher
{
    protected ExportMessage $message;

    public function __construct(Order $order)
    {
        /** @var CreateOrderCommandFactory $commandFactory */
        $commandFactory = Container::getContainer()
            ? Container::getContainer()?->get('Qualiteam\SkinActYotpoReviews\Core\Factory\Commands\CreateOrderCommandFactory')
            : null;
        $command        = $commandFactory->createCommand($order);

        $this->message = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}