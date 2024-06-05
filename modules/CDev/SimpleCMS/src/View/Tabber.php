<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View;

use XCart\Extender\Mapping\Extender;

/**
 * Tabber is a component allowing to organize your dialog into pages and
 * switch between the page using Tabs at the top.
 *
 * @Extender\Mixin
 */
abstract class Tabber extends \XLite\View\Tabber
{
    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        return \XLite::getController()->getTarget() === 'menus'
            ? parent::isTabsNavigationVisible()
              && !isset(\XLite\Core\Request::getInstance()->id)
            : parent::isTabsNavigationVisible();
    }
}
