<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Model\Repo\Image\Common;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Logo extends \XLite\Model\Repo\Image\Common\Logo
{

    public static function getFakeImageObject($path)
    {
        $obj = parent::getFakeImageObject($path);
        $obj->setFileName(basename($obj->getPath()));
        return $obj;
    }

    public function getLogo()
    {
        $path = \XLite\Core\Layout::getInstance()->getLogo();

        return $path
            ? static::getFakeImageObject($path)
            : null;
    }

    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getMobileLogo()
    {
        $path = \XLite\Core\Layout::getInstance()->getMobileLogo();

        return $path
            ? static::getFakeImageObject($path)
            : null;
    }

    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getFavicon()
    {
        $path = \XLite\Core\Layout::getInstance()->getFavicon();

        return $path
            ? static::getFakeImageObject($path)
            : null;
    }

    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getAppleIcon()
    {
        $path = \XLite\Core\Layout::getInstance()->getAppleIcon();

        return $path
            ? static::getFakeImageObject($path)
            : null;
    }

    public function getMailLogo()
    {
        $path = \XLite\Core\Layout::getInstance()->getMailLogo();

        return $path
            ? static::getFakeImageObject($path)
            : null;
    }

    public function getPdfLogo()
    {
        $path = \XLite\Core\Layout::getInstance()->getPdfLogo();

        return $path
            ? static::getFakeImageObject($path)
            : null;
    }
}
