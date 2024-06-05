<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Main menu
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SimpleCMS")
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
        return 'modules/QSL/HorizontalCategoriesMenu/primary_menu.twig';
    }

    /**
     * Check if display Home link
     *
     * @return boolean
     */
    protected function isShowHomeLink()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_show_home;
    }

    /**
     * is multicolumn layout selected
     *
     * @return boolean
     */
    public function isMulticolSubcategoriesView()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_use_multicolumn;
    }

    /**
     * Display item class as tag attribute
     *
     * @param integer $index Item index
     * @param mixed   $item  Item element
     *
     * @return string
     */
    protected function displayItemClass($index, $item)
    {
        $classes = ['leaf'];

        if (count($this->getItems()) === ($index + 1)) {
            $classes[] = 'last';
        }

        if ($item['active']) {
            $classes[] = 'active';
        }

        if ($item['hasSubmenus']) {
            $classes[] = 'has-sub';
        }

        return $classes ? ' class="' . implode(' ', $classes) . '"' : '';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true; /* even if there are no items in the top menu */
    }

    /**
     * Correct widget cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = $this->getCategoryId();

        return $params;
    }
}
