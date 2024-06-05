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
use XLite\Core\Database;
use XLite\Model\Product;
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\HydratorException;

class PushProductsCommand implements ICommand
{
    const ALREADY_IN_SKUVAULT = 'Product with same SKU exists';
    const STATUS_SUCCESS      = 'OK';

    private APIService      $api;
    private BaseConverter   $pushProductConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;
    private                 $productIds;

    public function __construct(
        APIService      $api,
        BaseConverter   $pushProductConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens,
        array           $productIds
    ) {
        $this->api                  = $api;
        $this->pushProductConverter = $pushProductConverter;
        $this->hydratorFactory      = $hydratorFactory;
        $this->getTokens            = $getTokens;
        $this->productIds           = $productIds;
    }

    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {
            $dto = [];

            foreach ($this->productIds as $id) {
                $hydrator = $this->hydratorFactory->hydrator($id, Product::class, $this->pushProductConverter);
                $dto[]    = $hydrator->getDTO();
            }

            if (!empty($dto)) {
                $result = $this->api->sendRequest(
                    'POST',
                    'products/createProducts',
                    [
                        'body' => json_encode([
                            'Items'       => $dto,
                            'TenantToken' => $tokens['TenantToken'],
                            'UserToken'   => $tokens['UserToken']
                        ])
                    ]
                );
                $this->processSuccess($result);
            }

        } catch (APIException $e) {
            $this->processError($e->getContents());
            throw new CommandException('Command failed', 0, $e);
        } catch (HydratorException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }

    protected function processSuccess($result)
    {
        if (
            isset($result['Status'])
            && $result['Status'] === self::STATUS_SUCCESS
        ) {
            foreach ($this->productIds as $id) {
                /** @var Product $product */
                $product = Database::getRepo(Product::class)->find($id);
                $product->setIsSkuvaultSynced(true);
                $product->setIsSkuvaultUpdateSynced(true);
                $this->createSuccessLogEntry($product);
            }

            Database::getEM()->flush();
        }
    }

    protected function processError(string $contents)
    {
        $this->createErrorLogEntries($contents);
    }

    protected function createSuccessLogEntry(Product $product)
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage('Product with SKU ' . $product->getSku() . ' is pushed to SkuVault')
            ->setOperation(OperationTypes::TYPE_CREATE_PRODUCT);
        Database::getEM()->persist($logEntry);
    }

    protected function createErrorLogEntries(string $contents)
    {
        $errors = json_decode($contents, true);

        if (isset($errors['Errors'])) {
            foreach ($errors['Errors'] as $error) {
                $logEntry = new Log();
                $logEntry->setDate(time())
                    ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
                    ->setStatus(SyncStatuses::STATUS_ERROR)
                    ->setMessage(var_export($error, true))
                    ->setOperation(OperationTypes::TYPE_CREATE_PRODUCT);

                if (in_array(self::ALREADY_IN_SKUVAULT, $error['ErrorMessages'])) {
                    $sku     = $error['Sku'];
                    $product = Database::getRepo(Product::class)->findOneBySku($sku);
                    $product->setIsSkuvaultSynced(true);
                }

                Database::getEM()->persist($logEntry);
            }
            Database::getEM()->flush();
        }
    }
}
