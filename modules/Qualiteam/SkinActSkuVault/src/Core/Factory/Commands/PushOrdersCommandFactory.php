<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory\Commands;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Command\Push\PushOrdersCommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;

class PushOrdersCommandFactory
{
    private APIService      $api;
    private BaseConverter   $pushOrderConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;

    public function __construct(
        APIService      $api,
        BaseConverter   $pushProductConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens
    ) {
        $this->api                  = $api;
        $this->pushOrderConverter = $pushProductConverter;
        $this->hydratorFactory      = $hydratorFactory;
        $this->getTokens            = $getTokens;
    }

    public function createCommand(array $ordersIds)
    {
        return new PushOrdersCommand($this->api, $this->pushOrderConverter, $this->hydratorFactory,
            $this->getTokens, $ordersIds);
    }
}
