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

class UpdateVariantsCommand implements ICommand
{
    const STATUS_SUCCESS = 'OK';

    private APIService      $api;
    private BaseConverter   $updateVariantConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;
    private array           $variantIds;

    public function __construct(
        APIService      $api,
        BaseConverter   $updateVariantConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens,
        array           $variantIds
    ) {
        $this->api                    = $api;
        $this->updateVariantConverter = $updateVariantConverter;
        $this->hydratorFactory        = $hydratorFactory;
        $this->getTokens              = $getTokens;
        $this->variantIds             = $variantIds;
    }

    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {
            $dto = [];

            foreach ($this->variantIds as $id) {
                $hydrator = $this->hydratorFactory->hydrator($id, ProductVariant::class, $this->updateVariantConverter);
                $dto[]    = $hydrator->getDTO();
            }

            if (!empty($dto)) {
                $items = array_chunk($dto, 100);
                $i = 0;
                foreach ($items as $dto) {
                    if ($i === 4) {
                        sleep(120);
                        $i = 0;
                    }

                    $result = $this->api->sendRequest(
                        'POST',
                        'products/updateProducts',
                        [
                            'body' => json_encode([
                                'Items'       => $dto,
                                'TenantToken' => $tokens['TenantToken'],
                                'UserToken'   => $tokens['UserToken']
                            ])
                        ]
                    );

                    $this->processSuccess($result);

                    sleep(1);
                    $i++;
                 }
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
            foreach ($this->variantIds as $id) {
                /** @var ProductVariant $variant */
                $variant = Database::getRepo(ProductVariant::class)->find($id);
                $this->createSuccessLogEntry($variant);
            }

            Database::getEM()->flush();
            Database::getEM()->clear();
        }
    }

    protected function createSuccessLogEntry(ProductVariant $variant)
    {
        $product  = $variant->getProduct();
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage('Variant with SKU ' . $variant->getSku() . ' of product with SKU ' . $product->getSku() . ' is updated in SkuVault')
            ->setOperation(OperationTypes::TYPE_UPDATE_PRODUCT);
        Database::getEM()->persist($logEntry);
    }

    protected function processError(string $contents)
    {
        $this->createErrorLogEntries($contents);
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
                    ->setOperation(OperationTypes::TYPE_UPDATE_PRODUCT);

                Database::getEM()->persist($logEntry);
            }
            Database::getEM()->flush();
        }
    }
}
