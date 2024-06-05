<?php

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Main menu
 *
 * @ListChild (list="layout.header", weight="450")
 */
class PrimaryMenu extends \XLite\View\Menu\Customer\Top
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'primary_menu/primary_menu_items.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isCheckoutLayout();
    }
}