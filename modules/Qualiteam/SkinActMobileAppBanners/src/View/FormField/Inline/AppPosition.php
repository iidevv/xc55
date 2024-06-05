<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\View\FormField\Inline;


use Qualiteam\SkinActMobileAppBanners\Model\BannerSlide;

class AppPosition extends \XLite\View\FormField\Inline\Base\Single
{

    protected function defineFieldClass()
    {
        return \Qualiteam\SkinActMobileAppBanners\View\FormField\Select\AppPosition::class;
    }

    protected function getViewValue(array $field)
    {
        $val = parent::getViewValue($field);

        $map = [];

        $map[BannerSlide::APP_POSITION_1] = static::t('SkinActMobileAppBanners APP_POSITION_1');
        $map[BannerSlide::APP_POSITION_2] = static::t('SkinActMobileAppBanners APP_POSITION_2');
        $map[BannerSlide::APP_POSITION_3] = static::t('SkinActMobileAppBanners APP_POSITION_3');

        return $map[$val] ?? static::t('SkinActMobileAppBanners APP_POSITION undefined');
    }
}