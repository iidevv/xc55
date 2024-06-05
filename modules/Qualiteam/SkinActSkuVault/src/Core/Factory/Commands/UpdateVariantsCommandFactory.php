<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory\Commands;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Command\Push\UpdateVariantsCommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;

class UpdateVariantsCommandFactory
{
    private APIService      $api;
    private BaseConverter   $updateVariantConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;

    public function __construct(
        APIService      $api,
        BaseConverter   $updateVariantConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens
    ) {
        $this->api                    = $api;
        $this->updateVariantConverter = $updateVariantConverter;
        $this->hydratorFactory        = $hydratorFactory;
        $this->getTokens              = $getTokens;
    }

    public function createCommand(array $variantsIds)
    {
        return new UpdateVariantsCommand($this->api, $this->updateVariantConverter, $this->hydratorFactory,
            $this->getTokens, $variantsIds);
    }
}
