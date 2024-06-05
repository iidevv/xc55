<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Gateway;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandsGateway
 */
class CommandsGateway implements ICommandsGateway
{
    /**
     * Consume commands
     *
     * @return void
     */
    public function consumeCommands(KernelInterface $kernel): void
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'messenger:consume',
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
    }
}
