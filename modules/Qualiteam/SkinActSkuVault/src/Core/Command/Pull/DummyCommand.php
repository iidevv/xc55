<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Pull;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Metadata\MetadataGateway;
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Command\Pull\Updater\UpdaterException;

/**
 * DummyCommand
 */
class DummyCommand implements ICommand
{
    /**
     * @var string
     */
    protected $externalData;

    private MetadataGateway $metadataGateway;

    private APIService $apiService;

    /**
     * @param array $externalData
     * @param MetadataGateway $metadataGateway
     * @param APIService $apiService
     */
    public function __construct(array $externalData, MetadataGateway $metadataGateway, APIService $apiService)
    {
        $this->externalData = $externalData;
        $this->metadataGateway = $metadataGateway;
        $this->apiService = $apiService;
    }

    /**
     * @inheritDoc
     */
    public function execute(): void
    {
        try {
            $this->apiService->sendRequest(
                'GET',
                'example'
            );

        } catch (APIException|UpdaterException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }
}
