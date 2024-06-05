<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Consumer;

use Qualiteam\SkinActSkuVault\Core\Gateway\ICommandsGateway;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandsConsumer
 */
class CommandsConsumer implements ICommandsConsumer
{
    protected $commandsGateway;

    /**
     * Constructor
     *
     * @param ICommandsGateway $commandsGateway
     *
     * @return void
     */
    public function __construct(ICommandsGateway $commandsGateway)
    {
        $this->commandsGateway = $commandsGateway;
    }

    /**
     * Consume commands
     *
     * @return void
     */
    public function consumeCommands(KernelInterface $kernel): void
    {
        $this->commandsGateway->consumeCommands($kernel);
    }
}
