<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("CDev\FileAttachments")
 */
class SelectFile extends \XLite\Controller\Admin\SelectFile
{
    /**
     * @param \XLite\Model\Base\Storage $storage
     *
     * @return string
     */
    protected function getAttachmentHash(\XLite\Model\Base\Storage $storage)
    {
        if ($storage->getStorageType() === \XLite\Model\Base\Storage::STORAGE_URL) {
            return \Includes\Utils\FileManager::getHash($storage->getSignedUrl(), true);
        }

        return parent::getAttachmentHash($storage);
    }
}
