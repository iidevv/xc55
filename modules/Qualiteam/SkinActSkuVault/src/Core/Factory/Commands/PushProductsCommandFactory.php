<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory\Commands;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Command\Push\PushProductsCommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;

class PushProductsCommandFactory
{
    private APIService      $api;
    private BaseConverter   $pushProductConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;

    public function __construct(
        APIService      $api,
        BaseConverter   $pushProductConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens
    ) {
        $this->api                  = $api;
        $this->pushProductConverter = $pushProductConverter;
        $this->hydratorFactory      = $hydratorFactory;
        $this->getTokens            = $getTokens;
    }

    public function createCommand(array $productsIds)
    {
        return new PushProductsCommand($this->api, $this->pushProductConverter, $this->hydratorFactory,
            $this->getTokens, $productsIds);
    }
}
