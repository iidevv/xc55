<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\View\FormField\Select;


use Qualiteam\SkinActMobileAppBanners\Model\BannerSlide;

class AppPosition extends \XLite\View\FormField\Select\Regular
{

    protected function getDefaultOptions()
    {
        $list = [];

        $list[BannerSlide::APP_POSITION_1] = static::t('SkinActMobileAppBanners APP_POSITION_1');
        $list[BannerSlide::APP_POSITION_2] = static::t('SkinActMobileAppBanners APP_POSITION_2');
        $list[BannerSlide::APP_POSITION_3] = static::t('SkinActMobileAppBanners APP_POSITION_3');

        return $list;
    }

}