<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XLite\Core\Cache\ExecuteCached;

class LogoMobile extends \XLite\View\Logo
{
    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    protected function getLogoImage()
    {
        $cacheParams = [
            get_class($this),
            'logoImage'
        ];

        return ExecuteCached::executeCachedRuntime(static function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getMobileLogo();
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

        return ExecuteCached::executeCachedRuntime(static function () {
            return \XLite\Logic\ImageResize\Generator::getImageSizes('XLite\Model\Image\Common\Logo', 'Mobile');
        }, $cacheParams);
    }
}
