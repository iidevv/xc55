<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View\Menu\Customer;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\ListChild;

/**
 * Primary menu
 *
 * @ListChild (list="amp.header.menu", weight="10")
 */
class Top extends \XLite\View\Menu\Customer\Top
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/modules/CDev/SimpleCMS/primary_menu_items.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return Manager::getRegistry()->isModuleEnabled('CDev', 'SimpleCMS');
    }
}
