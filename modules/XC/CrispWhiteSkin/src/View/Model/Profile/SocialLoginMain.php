<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * \XLite\View\Model\Profile\Main
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SocialLogin")
 */
class SocialLoginMain extends \XLite\View\Model\Profile\Main
{
    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        unset($result['social-login']);

        return $result;
    }
}
