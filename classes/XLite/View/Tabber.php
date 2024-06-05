<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabber is a component allowing to organize your dialog into pages and
 * switch between the page using Tabs at the top.
 *
 * @ListChild (list="admin.center", zone="admin", weight="1000")
 */
class Tabber extends \XLite\View\AView
{
    /**
     * Widget parameters names
     */
    public const PARAM_BODY   = 'body';
    public const PARAM_SWITCH = 'switch';

    /**
     * Lazy initialization cache
     *
     * @var array
     */
    protected $pages;

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return (!\XLite::isAdminZone()
                || 0 < count($this->getTabberPages())
            )
            && \XLite::getController()->checkAccess();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabber.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_BODY   => new \XLite\Model\WidgetParam\TypeString('Body template file', '', false),
            static::PARAM_SWITCH => new \XLite\Model\WidgetParam\TypeString('Switch', 'page', false),
        ];
    }

    /**
     * Get prepared pages array for tabber
     *
     * @return array
     */
    protected function getTabberPages()
    {
        if ($this->pages === null) {
            $this->pages = [];
            $url         = $this->get('url');
            $switch      = $this->getParam(static::PARAM_SWITCH);

            $dialogPages = \XLite::getController()->getTabPages();

            if (is_array($dialogPages)) {
                foreach ($dialogPages as $page => $title) {
                    $linkTemplate = null;
                    $subTabs      = [];
                    if (is_array($title)) {
                        $linkTemplate = $title['linkTemplate'] ?? null;

                        if (isset($title['subTabsWidget']) && class_exists($title['subTabsWidget'])) {
                            $subTabsWidgetParams = !empty($title['subTabsWidgetParams']) ? $title['subTabsWidgetParams'] : [];
                            $subTabsWidget       = new $title['subTabsWidget']($subTabsWidgetParams);
                            $subTabs             = $subTabsWidget->getTabsForSubmenu();
                            if (!is_array($subTabs) && count($subTabs) < 2) {
                                $subTabs = [];
                            }
                        }

                        $title = $title['title'];
                    }

                    $p       = new \XLite\Base();
                    $pageURL = preg_replace('/' . $switch . '=(\w+)/', $switch . '=' . $page, $url);
                    $p->set('url', $pageURL);
                    $p->set('title', $title);
                    $p->set('linkTemplate', $linkTemplate);
                    $p->set('key', $page);
                    $pageSwitch = sprintf($switch . '=' . $page);
                    $p->set('selected', (preg_match('/' . preg_quote($pageSwitch) . '(\Z|&)/Ss', $url)));
                    $p->set('subTabs', $subTabs);
                    $this->pages[] = $p;
                }
            }

            // if there is only one tab page, set it as a seleted with the default URL
            if (count($this->pages) === 1 || $this->getPage() === 'default') {
                $this->pages[0]->set('selected', $url);
            }
        }

        return $this->pages;
    }

    /**
     * Return body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return $this->getParam(static::PARAM_BODY) ?: $this->getPageTemplate();
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        return !(\XLite::getController() instanceof \XLite\Controller\Admin\Settings)
            && 1 < count($this->getTabberPages());
    }

    /**
     * JS Tab Controller. Init Tab "More", if the tabs do not fit in the line before the horizontal scroll
     *
     * @return array|string[]
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->needsMoreDropdownBehaviour()) {
            return array_merge(
                $list,
                ['product/tabs_menu/product_tabs_menu.js']
            );
        }

        return $list;
    }

    public function needsMoreDropdownBehaviour()
    {
        return \XLite\Core\Request::getInstance()->target === 'product';
    }
}
