<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Dispatcher;

use Qualiteam\SkinActYotpoReviews\Core\Factory\Commands\CreateProductCommandFactory;
use Qualiteam\SkinActYotpoReviews\Messenger\Message\ExportMessage;
use XCart\Container;
use XLite\Model\Product;

class CreateProductDispatcher
{
    protected ExportMessage $message;

    public function __construct(Product $product)
    {
        /** @var CreateProductCommandFactory $commandFactory */
        $commandFactory = Container::getContainer()
            ? Container::getContainer()?->get('Qualiteam\SkinActYotpoReviews\Core\Factory\Commands\CreateProductCommandFactory')
            : null;
        $command        = $commandFactory->createCommand($product);

        $this->message = new ExportMessage($command);
    }

    public function getMessage()
    {
        return $this->message;
    }
}