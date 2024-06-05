<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Messenger\Handler;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use XCart\Messenger\Message\ResizeImage;
use XLite\Core\Database;
use XLite\Model\Base\Image;

class ResizeImageHandler implements MessageHandlerInterface
{
    public function __invoke(ResizeImage $message)
    {
        /** @var Image $image */
        $image = Database::getRepo($message->getClass())->find($message->getId());

        if ($image) {
            $image->prepareSizes();
        }
    }
}
