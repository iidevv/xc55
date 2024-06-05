<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Messenger\Handler;

use Qualiteam\SkinActSkuVault\Core\Command\CommandException;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use XLite\InjectLoggerTrait;

class ExportHandler implements MessageHandlerInterface
{
    use InjectLoggerTrait;

    /**
     * @param ExportMessage $message
     * @return void
     */
    public function __invoke(ExportMessage $message): void
    {
        try {
            $message->getCommand()->execute();

            $this->getLogger()->debug(
                'Command complete',
                ['command' => $message->getCommand()]
            );
        } catch (CommandException $e) {
            $this->getLogger()->error(
                $e->getMessage(),
                ['exception' => $e]
            );
        }
    }
}
