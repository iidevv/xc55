<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Endpoint\GetTokens;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;
use Qualiteam\SkinActSkuVault\Model\Log;
use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use XC\ProductVariants\Model\ProductVariant;
use XLite\Core\Database;
use XLite\Model\Product;
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\HydratorException;

class PushVariantCommand implements ICommand
{
    private APIService      $api;
    private BaseConverter   $pushVariantConverter;
    private HydratorFactory $hydratorFactory;

    private GetTokens $getTokens;
    private           $variantId;

    public function __construct(
        APIService      $api,
        BaseConverter   $pushVariantConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens,
        int             $variantId
    ) {
        $this->api                  = $api;
        $this->pushVariantConverter = $pushVariantConverter;
        $this->hydratorFactory      = $hydratorFactory;
        $this->getTokens            = $getTokens;
        $this->variantId            = $variantId;
    }

    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {
            $hydrator = $this->hydratorFactory->hydrator($this->variantId, ProductVariant::class, $this->pushVariantConverter);

            $dto = $hydrator->getDTO();

            $dto['TenantToken'] = $tokens['TenantToken'];
            $dto['UserToken'] = $tokens['UserToken'];

            $result = $this->api->sendRequest(
                'POST',
                'products/createProduct',
                [
                    'body' => json_encode($dto)
                ]
            );

            $this->processResult($result, $this->variantId);

        } catch (APIException|HydratorException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }

    protected function processResult(array $result, int $variantId)
    {
        $status = $result['Status'];
        $errors = $result['Errors'];

        if ($status === 'OK' && empty($errors)) {
            $variant = Database::getRepo(ProductVariant::class)->find($variantId);
            $variant->setIsSkuvaultSynced(true);
            $this->createSuccessLogEntry($variantId);
        } else {
            $this->createErrorLogEntry($status, $errors);
        }
        Database::getEM()->flush();
    }

    protected function createSuccessLogEntry(int $variantId)
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage('Variant with id ' . $variantId . ' is pushed to SkuVault')
            ->setOperation(OperationTypes::TYPE_CREATE_PRODUCT);
        Database::getEM()->persist($logEntry);
    }

    protected function createErrorLogEntry(string $status, array $errors)
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
            ->setStatus(SyncStatuses::STATUS_ERROR)
            ->setMessage('Status: ' . $status . '; ' . 'errors: ' . var_export($errors, true))
            ->setOperation(OperationTypes::TYPE_CREATE_PRODUCT);
        Database::getEM()->persist($logEntry);
    }
}
