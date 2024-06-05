<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Model\Repo\Product\Attachment;

/**
 * Product sttachment's storages repository
 */
class Storage extends \XLite\Model\Repo\Base\Storage
{
    /**
     * Get storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'attachments';
    }

    /**
     * Get file system images storage root path
     *
     * @return string
     */
    public function getFileSystemRoot()
    {
        return LC_DIR_FILES . $this->getStorageName() . LC_DS;
    }

    /**
     * Get web images storage root path
     *
     * @return string
     */
    public function getWebRoot()
    {
        return LC_FILES_URL . '/' . $this->getStorageName() . '/';
    }
}
