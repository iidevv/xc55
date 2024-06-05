<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Consumer;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Interface ICommandsConsumer
 */
interface ICommandsConsumer
{
    /**
     * Consume commands
     *
     * @return void
     */
    public function consumeCommands(KernelInterface $kernel): void;
}
