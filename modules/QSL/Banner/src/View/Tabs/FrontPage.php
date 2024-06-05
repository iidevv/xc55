<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class FrontPage extends \XLite\View\Tabs\FrontPage
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        if (\XLite\Core\Config::getInstance()->QSL->Banner->hide_banner_rotation_feature) {
            unset($tabs['banner_rotation']);
        }

        return $tabs;
    }
}
