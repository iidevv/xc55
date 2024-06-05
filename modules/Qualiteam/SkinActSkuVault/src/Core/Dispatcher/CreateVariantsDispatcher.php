<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Dispatcher;

use Qualiteam\SkinActSkuVault\Core\Factory\Commands\PushVariantsCommandFactory;
use Qualiteam\SkinActSkuVault\Messenger\Message\ExportMessage;
use XC\ProductVariants\Model\ProductVariant;
use XCart\Container;
use XLite\Core\Database;

class CreateVariantsDispatcher
{
    protected ExportMessage $message;

    public function __construct()
    {
        $productsIds = Database::getRepo(ProductVariant::class)->findVariantIdsToCreateInSkuvault();

        /** @var PushVariantsCommandFactory $commandFactory */
        $commandFactory = Container::getContainer() ? Container::getContainer()->get('Qualiteam\SkinActSkuVault\Core\Factory\Commands\PushVariantsCommandFactory') : null;
        $command        = $commandFactory->createCommand($productsIds);
        $this->message  = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}
