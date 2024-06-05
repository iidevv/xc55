<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Module\CDev\AmazonS3Images\Model\Base;

use XCart\Extender\Mapping\Extender;

/**
 * Storage abstract store
 *
 * @Extender\Mixin
 * @Extender\Depend({"CDev\AmazonS3Images"})
 */
abstract class Image extends \XLite\Model\Base\Image
{
    /**
     * Get URL
     *
     * @return string
     */
    public function getGoogleFeedURL()
    {
        if ($this->getStorageType() == static::STORAGE_S3) {
            $url = $this->getURL();
        } else {
            $url = parent::getGoogleFeedURL();
        }

        return $url;
    }
}
