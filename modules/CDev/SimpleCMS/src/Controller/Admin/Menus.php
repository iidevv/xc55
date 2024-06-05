<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Controller\Admin;

use function array_pop;

class Menus extends \XLite\Controller\Admin\AAdmin
{
    /**
     * FIXME- backward compatibility
     *
     * @var array
     */
    protected $params = ['target', 'page'];

    protected $menu;

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage menus');
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        return \CDev\SimpleCMS\Model\Menu::getTypes();
    }

    /**
     * Get current page
     *
     * @return string
     */
    public function getPage()
    {
        return \XLite\Core\Request::getInstance()->page ?: \CDev\SimpleCMS\Model\Menu::MENU_TYPE_PRIMARY;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $menuItem = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')
                ->find((int) \XLite\Core\Request::getInstance()->id);
        return $menuItem
            ? $menuItem->getName()
            : static::t('Menus');
    }

    /**
     * Check if the option "Show default menu along with the custom one" is displayed
     *
     * @return bool
     */
    public function isVisibleShowDefaultOption()
    {
        return false;
    }

    /**
     * Add part to the location nodes list
     */
    protected function addBaseLocation()
    {
        if ($this->getPage() === \CDev\SimpleCMS\Model\Menu::MENU_TYPE_PRIMARY) {
            $this->addLocationNode(
                'Primary menu',
                $this->buildURL('pages', '', ['page' => 'menus_P'])
            );
        } else {
            $this->addLocationNode(
                'Footer menu',
                $this->buildURL('pages', '', ['page' => 'menus_F'])
            );
        }

        if (($menu = $this->getMenu()) && ($path = $menu->getPath())) {
            array_pop($path);

            foreach ($path as $item) {
                $this->addLocationNode(
                    $item->getName(),
                    $this->buildURL('menus', '', ['id' => $item->getMenuId(), 'page' => $this->getPage()])
                );
            }
        }
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = [];
        foreach (\CDev\SimpleCMS\Model\Menu::getTypes() as $k => $v) {
            $list[$k] = 'modules/CDev/SimpleCMS/menus/body.twig';
        }

        return $list;
    }

    protected function getMenu()
    {
        if ($this->menu === null) {
            $this->menu = \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')
                ->find((int) \XLite\Core\Request::getInstance()->id);
        }

        return $this->menu;
    }
}
