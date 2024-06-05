<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator;

use Psr\Log\LoggerInterface;

class LoggedHydrator implements IObjectHydrator
{
    /**
     * @var IObjectHydrator
     */
    private IObjectHydrator $hydrator;
    private LoggerInterface   $logger;

    /**
     * @param IObjectHydrator $hydrator
     * @param LoggerInterface $logger
     */
    public function __construct(IObjectHydrator $hydrator, LoggerInterface $logger)
    {
        $this->hydrator = $hydrator;
        $this->logger   = $logger;
    }

    public function getDTO(): array
    {
        try {
            $dto = $this->hydrator->getDTO();

            $this->logger->debug('DTO is ready', ['hydrator' => $this->hydrator, 'dto' => $dto]);

            return $dto;

        } catch (HydratorException $exception) {
            $this->logger->error('DTO isn\'t ready', ['hydrator' => $this->hydrator]);
            throw $exception;
        }
    }
}
