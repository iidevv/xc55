<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\View;


use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="banner.promo", zone="admin", weight="20")
 */
class BannersListDescription extends \XLite\View\AView
{

    protected function getDescription()
    {
        return static::t('SkinActMobileAppBanners page description');
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActMobileAppBanners/BannersListDescription.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActMobileAppBanners/BannersListDescription.css';
        return $list;
    }
}