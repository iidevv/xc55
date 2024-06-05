<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\Menu\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Top extends \XLite\View\Menu\Customer\Top
{
    /**
     * Correct widget cach parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $auth = \XLite\Core\Auth::getInstance();

        $params = parent::getCacheParameters();

        $params[] = $auth->isAnonymous();

        $params[] = $auth->isLogged() && $auth->getProfile() && $auth->getProfile()->getMembership()
            ? $auth->getProfile()->getMembership()->getMembershipId()
            : null;

        $params[] = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')->getVersion();
        $params[] = LC_USE_CLEAN_URLS;

        return $params;
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

        $menus = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')->getMenusPlainList($cnd);
        foreach ($menus as $menuItem) {
            $menu[] = [
                'id'          => $menuItem->getId(),
                'label'       => $menuItem->getName(),
                'depth'       => $menuItem->getDepth(),
                'controller'  => $menuItem->getLinkController(),
                'url'         => $menuItem->getUrl(),
                'hasSubmenus' => $menuItem->getSubmenusCountConditional() > 0,
            ];
        }

        $menu = \XLite\Core\Config::getInstance()->CDev->SimpleCMS->show_default_menu
            ? array_merge(
                parent::defineItems(),
                $menu
            )
            : ($menu ?: parent::defineItems());

        return $menu;
    }

    /**
     * Define Menu item
     *
     * @param \CDev\SimpleCMS\Model\Menu $menuItem Menu item
     *
     * @return array
     */
    protected function defineItem(\CDev\SimpleCMS\Model\Menu $menuItem)
    {
        return [
            'url'           => $menuItem->getURL(),
            'label'         => $menuItem->getName(),
            'controller'    => $menuItem->getLinkController(),
        ];
    }

    /**
     * Previous menu depth
     *
     * @var integer
     */
    protected $prevMenuDepth = 0;

    /**
     * Is first element
     *
     * @var integer
     */
    protected $isFirst = true;

    /**
     * Return the CSS files for the menu
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/SimpleCMS/css/primary_menu.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SimpleCMS/primary_menu_items.twig';
    }

    /**
     * Return next menu level or not
     *
     * @param integer $menuDepth Level depth
     *
     * @return boolean
     */
    protected function isLevelUp($menuDepth)
    {
        $result = false;
        if ($menuDepth > $this->prevMenuDepth) {
            $result = true;
            $this->prevMenuDepth = $menuDepth;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Return previous menu level or not
     *
     * @param integer $menuDepth Level depth
     *
     * @return boolean
     */
    protected function isLevelBelow($menuDepth)
    {
        $result = false;
        if ($menuDepth < $this->prevMenuDepth) {
            $result = true;
            $this->prevMenuDepth = $menuDepth;
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Return is level changed
     *
     * @return boolean
     */
    protected function closeMenuList($menuDepth = 0)
    {
        $result = '';
        for ($i = $menuDepth; $i < $this->prevMenuDepth; $i++) {
            $result .= '</ul></li>';
        }
        $this->prevMenuDepth = $menuDepth;

        return $result;
    }

    /**
     * Return is first element
     *
     * @return boolean
     */
    protected function isFirstElement()
    {
        $result = $this->isFirst;
        $this->isFirst = false;

        return $result;
    }

    /**
     * Reset $isFirst
     *
     * @return void
     */
    protected function resetFirstElement()
    {
        $this->isFirst = true;
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
        $class = parent::displayItemClass($index, $item);

        return !empty($item['hasSubmenus'])
            ?  preg_replace('/"$/', ' has-sub"', $class)
            :  $class;
    }
}
