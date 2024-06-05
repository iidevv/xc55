<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Operator;

/**
 * @Extender\Mixin
 */
abstract class Storage extends \XLite\Controller\Admin\Storage
{
    /**
     * Read storage
     *
     * @param \XLite\Model\Base\Storage $storage Storage
     *
     * @return void
     */
    protected function readStorage(\XLite\Model\Base\Storage $storage)
    {
        if (
            $storage instanceof \CDev\Egoods\Model\Product\Attachment\Storage
            && $storage->canBeSigned()
            && !$storage->isFileAvailable()
        ) {
            Operator::redirect($storage->getSignedUrl());
            return;
        }

        parent::readStorage($storage);
    }
}
