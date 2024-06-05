<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\Account;

use XCart\Extender\Mapping\ListChild;

/**
 * Account top menu node
 *
 * @ListChild (list="admin.main.page.header.right", weight="300", zone="admin")
 */
class TopMenuNode extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'menu/account/top_menu_node.twig';
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Auth::getInstance()->isAdmin();
    }
}
