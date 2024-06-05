<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Main menu
 *
 * @ListChild (list="slidebar.menu", weight="50")
 * @ListChild (list="header.menu", weight="50")
 */
class Top extends \XLite\View\Menu\Customer\ACustomer
{
    // Name of parameter to use cut list (only <li></li> tags) instead of full list
    public const PARAM_IS_SLIDEBAR = 'isSlidebar';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/header/main_menu_items.twig';
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_IS_SLIDEBAR => new \XLite\Model\WidgetParam\TypeBool('Is use cut list', false),
        ];
    }

    /**
     * Correct widget cach parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = $this->isSlidebar();

        return $params;
    }

    /**
     * Return true if menu list should be cut
     *
     * @return boolean
     */
    protected function isSlidebar()
    {
        return $this->getParam(static::PARAM_IS_SLIDEBAR);
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
