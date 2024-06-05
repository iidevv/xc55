<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Dispatcher;

use Qualiteam\SkinActSkuVault\Core\Factory\Commands\PushOrdersCommandFactory;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use Qualiteam\SkinActSkuVault\Model\Log;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XCart\Container;
use XLite\Core\Database;
use XLite\Model\Order;

class PushOrdersDispatcher
{
    protected ExportMessage $message;

    public function __construct()
    {
        /** @var Order[] $orders */
        $orders = Database::getRepo(Order::class)->findOrdersForSkuvaultSync();

        $orderIds = [];
        foreach ($orders as $order) {

            $skipOrder = true;
            foreach ($order->getItems() as $item) {
                if (!$item->getProduct()->isSkippedFromSync()) {
                    $skipOrder = false;
                    break;
                }
            }

            $skuvaultStatuses = $order->getStatusesMapXcToSkuvault();

            if (!$skuvaultStatuses) {
                $this->addStatusMappingNotFoundError($order);
                $skipOrder = true;
            }

            if ($skipOrder) {
                $order->setSkuvaultNotSync(Order::NOT_SYNC_YES);
            }

            if (!$skipOrder && $skuvaultStatuses) {
                $orderIds[] = ['order_id' => $order->getOrderId(), 'order_number' => $order->getOrderNumber()];
            }
        }

        Database::getEM()->flush();

        /** @var PushOrdersCommandFactory $commandFactory */
        $commandFactory = Container::getContainer() ? Container::getContainer()->get('Qualiteam\SkinActSkuVault\Core\Factory\Commands\PushOrdersCommandFactory') : null;
        $command        = $commandFactory->createCommand($orderIds);
        $this->message  = new ExportMessage($command);
    }

    protected function addStatusMappingNotFoundError(Order $order): void
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
            ->setStatus(SyncStatuses::STATUS_ERROR)
            ->setMessage('Status map for order number ' . $order->getPrintableOrderNumber() . ' is not found, skipping.')
            ->setOperation(OperationTypes::TYPE_ADD_SALE);
        Database::getEM()->persist($logEntry);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
