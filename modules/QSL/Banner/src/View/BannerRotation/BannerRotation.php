<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\BannerRotation;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class BannerRotation extends \XLite\View\BannerRotation\BannerRotation
{
    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return !\XLite\Core\Config::getInstance()->QSL->Banner->hide_banner_rotation_feature
            && parent::isVisible();
    }
}
