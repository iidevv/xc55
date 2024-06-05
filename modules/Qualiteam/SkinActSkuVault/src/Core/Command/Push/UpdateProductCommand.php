<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push;

use Qualiteam\SkinActSkuVault\Core\API\APIService;
use Qualiteam\SkinActSkuVault\Core\Command\ICommand;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Factory\HydratorFactory;
use XLite\Model\Product;
use Qualiteam\SkinActSkuVault\Core\API\APIException;
use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\HydratorException;

class UpdateProductCommand implements ICommand
{
    private APIService      $api;
    private BaseConverter   $updateProductConverter;
    private HydratorFactory $hydratorFactory;
    private                 $productId;

    public function __construct(APIService $api, BaseConverter $updateProductConverter, HydratorFactory $hydratorFactory, int $productId)
    {
        $this->api                    = $api;
        $this->updateProductConverter = $updateProductConverter;
        $this->hydratorFactory        = $hydratorFactory;
        $this->productId              = $productId;
    }

    public function execute(): void
    {
        try {
            $hydrator = $this->hydratorFactory->hydrator($this->productId, Product::class, $this->updateProductConverter);

            $dto = $hydrator->getDTO();

            $this->api->sendRequest(
                'POST',
                'products/updateProduct',
                [
                    'body' => json_encode($dto)
                ]
            );
        } catch (APIException|HydratorException $e) {
            throw new CommandException('Command failed', 0, $e);
        }
    }
}
