<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Footer menu
 *
 * @ListChild (list="layout.main.footer", weight="100")
 */
class Footer extends \XLite\View\Menu\Customer\ACustomer
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/footer/footer_menu.twig';
    }

    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        return [];
    }
}
