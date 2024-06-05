<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Menu\Mobile;

use XCart\Extender\Mapping\ListChild;

/**
 * Educational videos menu item
 *
 * @ListChild (list="slidebar.navbar.account", weight="30", zone="customer")
 */
class EducationalVideos extends \Qualiteam\SkinActVideoFeature\View\Menu\Customer\EducationalVideos
{
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActVideoFeature/layout/header/mobile_header_parts/navbar/account/educational_videos.twig';
    }
}
