<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\Extender;
use XLite;

/**
 * @Extender\Mixin
 */
abstract class AView extends \XLite\View\AView
{
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'js/dropdown.js',
                'js/mobile-menu/script.js',
            ]
        );
    }

    public function isInCheckoutSection(): bool
    {
        $controller = XLite::getController();

        return method_exists($controller, 'isCheckoutLayout') && $controller->isCheckoutLayout();
    }
}
