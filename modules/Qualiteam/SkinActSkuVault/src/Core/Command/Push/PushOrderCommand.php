<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;
use XLite\Model\Order;
use XLite\Model\Product;
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\HydratorException;

class PushOrderCommand implements ICommand
{
    private APIService      $api;
    private BaseConverter   $pushOrderConverter;
    private HydratorFactory $hydratorFactory;
    private                 $orderId;

    public function __construct(APIService $api, BaseConverter $pushOrderConverter, HydratorFactory $hydratorFactory, int $orderId)
    {
        $this->api                = $api;
        $this->pushOrderConverter = $pushOrderConverter;
        $this->hydratorFactory    = $hydratorFactory;
        $this->orderId            = $orderId;
    }

    public function execute(): void
    {
        try {
            $hydrator = $this->hydratorFactory->hydrator($this->orderId, Order::class, $this->pushOrderConverter);

            $dto = $hydrator->getDTO();

            $this->api->sendRequest(
                'POST',
                'sales/syncOnlineSale',
                [
                    'body' => json_encode($dto)
                ]
            );
        } catch (APIException|HydratorException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }
}
