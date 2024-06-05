<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model\Repo\Image\Common;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Logo extends \XLite\Model\Repo\Image\Common\Logo
{
    /**
     * @return \XLite\Model\Image\Common\Logo
     */
    public function getLogo()
    {
        $logo = parent::getLogo();

        if ($logo && $logo instanceof \XLite\Model\Image\Common\Logo) {
            $logo->setAlt(\XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo_alt);
        }

        return $logo;
    }
}
