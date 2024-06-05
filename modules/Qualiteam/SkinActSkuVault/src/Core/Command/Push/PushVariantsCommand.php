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
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\HydratorException;

class PushVariantsCommand implements ICommand
{
    const ALREADY_IN_SKUVAULT = 'Product variant with same SKU exists';
    const STATUS_SUCCESS      = 'OK';

    private APIService      $api;
    private BaseConverter   $pushVariantConverter;
    private HydratorFactory $hydratorFactory;
    private GetTokens       $getTokens;
    private                 $variantIds;

    public function __construct(
        APIService      $api,
        BaseConverter   $pushVariantConverter,
        HydratorFactory $hydratorFactory,
        GetTokens       $getTokens,
        array           $variantIds
    ) {
        $this->api                  = $api;
        $this->pushVariantConverter = $pushVariantConverter;
        $this->hydratorFactory      = $hydratorFactory;
        $this->getTokens            = $getTokens;
        $this->variantIds           = $variantIds;
    }

    public function execute(): void
    {
        $tokens = $this->getTokens->getData();

        try {
            $dto = [];

            foreach ($this->variantIds as $id) {
                $hydrator = $this->hydratorFactory->hydrator($id, ProductVariant::class, $this->pushVariantConverter);
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
                            'UserToken'   => $tokens['UserToken'],
                        ]),
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
            foreach ($this->variantIds as $id) {
                /** @var ProductVariant $variant */
                $variant = Database::getRepo(ProductVariant::class)->find($id);
                $variant->setIsSkuvaultSynced(true);
                $this->createSuccessLogEntry($variant);
            }

            Database::getEM()->flush();
        }
    }

    protected function createSuccessLogEntry(ProductVariant $variant)
    {
        $logEntry = new Log();
        $logEntry->setDate(time())
            ->setDirection(Directions::DIR_XC_TO_SKUVAULT)
            ->setStatus(SyncStatuses::STATUS_SUCCESS)
            ->setMessage('Product variant with SKU ' . $variant->getSku() . ' is pushed to SkuVault')
            ->setOperation(OperationTypes::TYPE_CREATE_PRODUCT);
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
                    ->setOperation(OperationTypes::TYPE_CREATE_PRODUCT);

                if (in_array(self::ALREADY_IN_SKUVAULT, $error['ErrorMessages'])) {
                    $sku     = $error['Sku'];
                    $variant = Database::getRepo(ProductVariant::class)->findOneBySku($sku);
                    $variant->setIsSkuvaultSynced(true);
                }

                Database::getEM()->persist($logEntry);
            }
            Database::getEM()->flush();
        }
    }
}
