<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Factory;

use Psr\Log\LoggerInterface;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\Hydrator;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\IObjectHydrator;
use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\LoggedHydrator;

class HydratorFactory
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function hydrator(string $localId, string $entityName, BaseConverter $dto): IObjectHydrator
    {
        return new LoggedHydrator(
            new Hydrator($localId, $entityName, $dto), $this->logger
        );
    }
}
