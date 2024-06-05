<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Dispatcher;

use Qualiteam\SkinActSkuVault\Core\Factory\Commands\PushVariantCommandFactory;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use XCart\Container;

class CreateVariantDispatcher
{
    protected ExportMessage $message;

    public function __construct(int $productsId)
    {
        /** @var PushVariantCommandFactory $commandFactory */
        $commandFactory = Container::getContainer() ? Container::getContainer()->get('Qualiteam\SkinActSkuVault\Core\Factory\Commands\PushVariantCommandFactory') : null;
        $command        = $commandFactory->createCommand($productsId);

        $this->message  = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
