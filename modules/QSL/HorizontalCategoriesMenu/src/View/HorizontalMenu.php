<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View;

/**
 * Main menu
 *
 * ListChild (list="header.menu", weight="50")
 * anyway it was hidden by the SimpleSMC module
 */
class HorizontalMenu extends \XLite\View\Menu\Customer\ACustomer
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/HorizontalCategoriesMenu/top_menu.twig';
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
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        $menu = [];
        $cnd = new \XLite\Core\CommonCell();
        $cnd->type = \CDev\SimpleCMS\Model\Menu::MENU_TYPE_PRIMARY;
        $cnd->enabled = true;
        $cnd->visibleFor = [
            'AL',
            (\XLite\Core\Auth::getInstance()->isLogged() ? 'L' : 'A'),
        ];

        foreach (\XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')->search($cnd) as $v) {
            $menu[] = [
                'url'           => $v->getURL(),
                'label'         => $v->getName(),
                'controller'    => $v->getLinkController(),
            ];
        }

        return $menu;
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true; /* cached before in View\Menu\Customer\Top  */
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return !$this->isCheckoutLayout();
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
        /*
        if (0 == $index && !$this->isShowHomeLink()) {
            $classes[] = 'first';
        }
        */
        if (count($this->getItems()) === ($index + 1)) {
            $classes[] = 'last';
        }

        if ($item['active']) {
            $classes[] = 'active';
        }

        return $classes ? ' class="' . implode(' ', $classes) . '"' : '';
    }
}
