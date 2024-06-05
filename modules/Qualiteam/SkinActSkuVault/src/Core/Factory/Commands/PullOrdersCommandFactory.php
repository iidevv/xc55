<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory\Commands;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\Pull\PullOrdersCommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;

class PullOrdersCommandFactory
{
    private APIService      $api;
    private GetTokens       $getTokens;

    public function __construct(
        APIService      $api,
        GetTokens       $getTokens
    ) {
        $this->api                  = $api;
        $this->getTokens            = $getTokens;
    }

    public function createCommand(string $fromDate, string $toDate)
    {
        return new PullOrdersCommand($this->api, $this->getTokens, $fromDate, $toDate);
    }
}
