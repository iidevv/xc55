<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Modules\XC\ThemeTweaker\View;


use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XLite\Core\Session;

/**
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ThemeTweaker")
 */
class ThemeTweakerPanel extends \XC\ThemeTweaker\View\ThemeTweakerPanel
{
    public static function isThemeTweakerEnabled()
    {
        $requestURI = (string)(Request::getInstance()->getServerData()['REQUEST_URI'] ?? '');
        $result = (bool)Session::getInstance()->{self::SESSION_VARIABLE};
        if (
            $result
            && (
                mb_strpos($requestURI, '?shopKey=') !== false
                || mb_strpos($requestURI, '&shopKey=') !== false
            )
        ) {
            self::switchThemeTweaker(false);
            $result = false;
        }
        return $result;
    }

}