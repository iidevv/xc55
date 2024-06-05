<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory\Commands;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Command\Push\PushVariantsCommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;

class PushVariantsCommandFactory
{
    private APIService      $api;
    private BaseConverter   $pushVariantConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;

    public function __construct(
        APIService      $api,
        BaseConverter   $pushVariantConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens
    ) {
        $this->api                  = $api;
        $this->pushVariantConverter = $pushVariantConverter;
        $this->hydratorFactory      = $hydratorFactory;
        $this->getTokens            = $getTokens;
    }

    public function createCommand(array $variantIds)
    {
        return new PushVariantsCommand($this->api, $this->pushVariantConverter, $this->hydratorFactory,
            $this->getTokens, $variantIds);
    }
}
