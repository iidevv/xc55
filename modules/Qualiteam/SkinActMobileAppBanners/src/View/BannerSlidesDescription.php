<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\View;


use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;

/**
 * @ListChild (list="admin.h1.after", zone="admin", weight="20")
 */
class BannerSlidesDescription extends \XLite\View\AView
{

    public function isVisible()
    {
        return Request::getInstance()->page === 'images'
            && Request::getInstance()->target === 'banner_edit'
            && $this->getBanner()
            && $this->getBanner()->getForMobileOnly();
    }

    protected function getDescription()
    {
        return static::t('SkinActMobileAppBanners BannerSlidesDescription page description');
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActMobileAppBanners/BannerSlidesDescription.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActMobileAppBanners/BannerSlidesDescription.css';
        return $list;
    }
}