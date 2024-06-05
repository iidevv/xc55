<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View;

class MenuFormattedPath extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'menus',
            ]
        );
    }

    /**
     * Check if the widget is visible
     *
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return the CSS files for the widget
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/SimpleCMS/menu_formatted_path/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SimpleCMS/menu_formatted_path/body.twig';
    }

    /**
     * Check if menu is current
     *
     * @return bool
     */
    protected function isCurrentMenu(\CDev\SimpleCMS\Model\Menu $menu)
    {
        return $this->getMenu() === $menu;
    }

    /**
     * Get menu
     *
     * @return \CDev\SimpleCMS\Model\Menu
     */
    protected function getMenu()
    {
        return \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu')
                ->find((int) \XLite\Core\Request::getInstance()->id);
    }

    /**
     * Get path of current menu
     *
     * @return array
     */
    protected function getMenuPath()
    {
        return $this->getMenu()->getPath();
    }

    /**
     * Get type of current menu
     *
     * @return string
     */
    protected function getType()
    {
        return \XLite\Core\Request::getInstance()->page;
    }
}
