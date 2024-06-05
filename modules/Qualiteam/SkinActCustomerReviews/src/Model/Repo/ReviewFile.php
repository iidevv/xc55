<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\Model\Repo;


class ReviewFile extends  \XLite\Model\Repo\Base\Storage
{
    public function getStorageName()
    {
        return 'review_files';
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

    public function getFileSystemCacheRoot($sizeName)
    {
        return LC_DIR_CACHE_IMAGES . $this->getStorageName() . LC_DS . $sizeName . LC_DS;
    }
}