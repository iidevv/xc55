<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\Controller\Admin;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class BannerEdit extends \QSL\Banner\Controller\Admin\BannerEdit
{
    public function getPages()
    {
        $list = parent::getPages();

        if ($this->getBanner()->getId()
            && $this->getBanner()->getForMobileOnly()
        ) {
            unset($list['codes']);
        }

        return $list;
    }

}