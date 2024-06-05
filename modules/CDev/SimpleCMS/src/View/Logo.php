<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCached;

/**
 * @Extender\Mixin
 */
class Logo extends \XLite\View\Logo
{
    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [$this->isMobileDevice()]
        );
    }

    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    protected function getLogoImage()
    {
        $cacheParams = [
            get_class($this),
            'logoImage'
        ];

        return ExecuteCached::executeCachedRuntime(function () {
            $logo = null;

            if ($this->isMobileDevice()) {
                $logo = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getMobileLogo();
            }

            if (!$logo) {
                $logo = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getLogo();
            }

            return $logo;
        }, $cacheParams);
    }

    /**
     * @return array
     */
    protected function getSizes()
    {
        $cacheParams = [
            get_class($this),
            'getSizes'
        ];

        return ExecuteCached::executeCachedRuntime(function () {
            $sizes = null;

            if (
                $this->isMobileDevice()
                && \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getMobileLogo()
                && \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getMobileLogo() != \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getLogo()
            ) {
                $sizes = \XLite\Logic\ImageResize\Generator::getImageSizes('XLite\Model\Image\Common\Logo', 'Mobile');
            }

            if (!$sizes) {
                $sizes = \XLite\Logic\ImageResize\Generator::getImageSizes('XLite\Model\Image\Common\Logo', 'Default');
            }

            return $sizes;
        }, $cacheParams);
    }
}
