<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory\Commands;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Command\Push\PushProductsCommand;
use Qualiteam\SkinActSkuVault\Core\Command\Push\UpdateProductsCommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;

class UpdateProductsCommandFactory
{
    private APIService      $api;
    private BaseConverter   $updateProductConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;

    public function __construct(
        APIService      $api,
        BaseConverter   $updateProductConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens
    ) {
        $this->api                  = $api;
        $this->updateProductConverter = $updateProductConverter;
        $this->hydratorFactory      = $hydratorFactory;
        $this->getTokens            = $getTokens;
    }

    public function createCommand(array $productsIds)
    {
        return new UpdateProductsCommand($this->api, $this->updateProductConverter, $this->hydratorFactory,
            $this->getTokens, $productsIds);
    }
}
