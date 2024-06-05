<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Skin;

/**
 * Abstract view
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    /**
     * Current skin is crisp white?
     *
     * @return boolean
     */
    public function isCrispWhiteOAuth2Client()
    {
        return Skin::getInstance()->getCurrentSkinModuleId() == 'XC-CrispWhiteSkin';
    }
}
