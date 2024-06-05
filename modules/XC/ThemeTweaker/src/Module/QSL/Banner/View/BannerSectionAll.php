<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Module\QSL\Banner\View;

use XCart\Extender\Mapping\Extender;

/**
 * Proxy class for story builder to get banners on very page
 * @Extender\Depend ("QSL\Banner")
 */
class BannerSectionAll extends \QSL\Banner\View\Customer\ABannerSection
{
    /*
     * Get access to protected property. Used to get banners body.
     */
    public function getBannerBoxesAll()
    {
        return $this->getBannerBoxes();
    }
}
