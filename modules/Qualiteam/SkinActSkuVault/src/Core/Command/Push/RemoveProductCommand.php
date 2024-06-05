<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Model\Log;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XLite\Core\Config;
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use XLite\Core\Database;

class RemoveProductCommand implements ICommand
{
    private APIService $api;
    private GetTokens  $getTokens;
    private string     $sku;
    private int        $quantity;
    private string     $reason;

    public function __construct(
        APIService $api,
        GetTokens  $getTokens,
        string     $sku,
        int        $quantity,
        string     $reason
    ) {
        $this->api       = $api;
        $this->getTokens = $getTokens;
        $this->sku       = $sku;
        $this->quantity  = $quantity;
        $this->reason    = $reason;
    }

    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {

            $body = [
                'WarehouseId'  => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_warehouse,
                'LocationCode' => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_location,
                'Sku'          => $this->sku,
                'Quantity'     => $this->quantity,
                'Reason'       => $this->reason,
                'TenantToken'  => $tokens['TenantToken'],
                'UserToken'    => $tokens['UserToken'],
            ];

            $result = $this->api->sendRequest(
                'POST',
                'inventory/removeItem',
                [
                    'body' => json_encode($body)
                ]
            );

            $this->processResult($result);

        } catch (APIException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }

    protected function processResult(array $result): void
    {
        if (isset($result['RemoveItemStatus'])) {
            $this->addLogEntry($result['RemoveItemStatus']);
        }
    }

    protected function addLogEntry(string $status): void
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage($status)
            ->setOperation(OperationTypes::TYPE_DELETE_PRODUCT_SYNC);
        Database::getEM()->persist($logEntry);
        Database::getEM()->flush();
    }
}
