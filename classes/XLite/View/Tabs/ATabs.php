<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * ATabs is a component allowing you to display multiple widgets as tabs depending
 * on their targets
 */
abstract class ATabs extends \XLite\View\AView
{
    /** @var array Tabs */
    protected $tabs = [];

    /** @var array Cached result of the getTabs() method */
    protected $processedTabs;

    /**
     * Define tabs
     *
     * Information on tab widgets and their targets defined as an array(tab) descriptions:
     *
     *      array(
     *          $target => array(
     *              'weight'   => $weight // Weight of the tab
     *              'title'    => $tabTitle,
     *              'widget'   => $optionalWidgetClass,
     *              'template' => $optionalWidgetTemplate,
     *          ),
     *          ...
     *      );
     *
     * If a widget class is not specified for a target, the ATabs descendant will be used as the widget class.
     * If a template is not specified for a target, it will be used from the tab widget class.
     *
     * @return array
     */
    abstract protected function defineTabs();

    /**
     * Define tabs
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->tabs = $this->defineTabs();
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $tab = $this->getSelectedTab();
        if ($tab !== null && !empty($tab['jsFiles'])) {
            if (is_array($tab['jsFiles'])) {
                $list = array_merge($list, $tab['jsFiles']);
            } else {
                $list[] = $tab['jsFiles'];
            }
        }

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $tab = $this->getSelectedTab();
        if ($tab !== null && !empty($tab['cssFiles'])) {
            if (is_array($tab['cssFiles'])) {
                $list = array_merge($list, $tab['cssFiles']);
            } else {
                $list[] = $tab['cssFiles'];
            }
        }

        return $list;
    }

    /**
     * Checks whether no widget class is specified for the selected tab
     *
     * @return bool
     */
    public function isTemplateOnlyTab()
    {
        $tab = $this->getSelectedTab();

        return $tab !== null && empty($tab['widget']) && !empty($tab['template']);
    }

    /**
     * Checks whether both a template and a widget class are specified for the selected tab
     *
     * @return bool
     */
    public function isFullWidgetTab()
    {
        $tab = $this->getSelectedTab();

        return $tab !== null && !empty($tab['widget']) && !empty($tab['template']);
    }

    /**
     * Checks whether no template is specified for the selected tab
     *
     * @return bool
     */
    public function isWidgetOnlyTab()
    {
        $tab = $this->getSelectedTab();

        return $tab !== null && !empty($tab['widget']) && empty($tab['template']);
    }

    /**
     * Returns a widget class name for the selected tab
     *
     * @return string
     */
    public function getTabWidget()
    {
        $tab = $this->getSelectedTab();

        return $tab['widget'] ?? '';
    }

    /**
     * Returns a template name for the selected tab
     *
     * @return string
     */
    public function getTabTemplate()
    {
        $tab = $this->getSelectedTab();

        return $tab['template'] ?? '';
    }

    /**
     * Checks whether no template is specified for the selected tab
     *
     * @return bool
     */
    public function isCommonTab()
    {
        $tab = $this->getSelectedTab();

        return $tab !== null && empty($tab['widget']) && empty($tab['template']);
    }

    /**
     * Flag: display (true) or hide (false) tabs
     *
     * @return bool
     */
    protected function isWrapperVisible()
    {
        return true;
    }

    /**
     * Returns the default widget template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs.twig';
    }

    /**
     * Returns the current target
     *
     * @return string
     */
    protected function getCurrentTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Returns a list of targets for which the tabs are visible
     *
     * @return array
     */
    protected function getTabTargets()
    {
        $list = [];

        foreach ($this->tabs as $target => $tab) {
            $list[] = $tab['url_params']['target'] ?? $target;

            foreach ($tab['references'] ?? [] as $reference) {
                if (isset($reference['target'])) {
                    $list[] = $reference['target'];
                }
            }
        }

        return $list;
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isCurrentTargetInTabTargets();
    }

    /**
     * @return bool
     */
    protected function isCurrentTargetInTabTargets()
    {
        return in_array($this->getCurrentTarget(), $this->getTabTargets(), true);
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return bool
     */
    protected function isTabsNavigationVisible()
    {
        return 1 < count($this->getTabs());
    }

    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL($target);
    }

    /**
     * Checks whether a tab is selected
     *
     * @param string $target Tab target
     *
     * @return bool
     */
    protected function isSelectedTab($target, $params = [])
    {
        $params['target'] = $target;

        return $this->isCurrentPage($params);
    }

    /**
     * @param array $references
     *
     * @return bool
     */
    protected function isSelectedTabByReference(array $references)
    {
        foreach ($references as $reference) {
            if ($this->isCurrentPage($reference)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    protected function isCurrentPage(array $params): bool
    {
        foreach ($params as $name => $value) {
            if (\XLite\Core\Request::getInstance()->{$name} !== (string) $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns default values for a tab description
     *
     * @return array
     */
    protected function getDefaultTabValues()
    {
        return [
            'title'    => '',
            'widget'   => '',
            'template' => '',
            'class'    => '',
        ];
    }

    /**
     * Sorting the tabs according their weight
     *
     * @return array
     */
    protected function prepareTabs()
    {
        $tabs = $this->tabs;
        // Manage the omitted weights of tabs
        $index = 1;
        foreach ($tabs as $target => $tab) {
            if (!isset($tab['weight'])) {
                $tabs[$target]['weight'] = $index;
            }
            $index++;
        }
        // Sort the tabs via compareTabs method
        uasort($tabs, [$this, 'compareTabs']);

        return $tabs;
    }

    /**
     * This method is used for comparing tabs
     * By default they are compared according their weight
     *
     * @param array $tab1
     * @param array $tab2
     *
     * @return bool
     */
    public function compareTabs($tab1, $tab2)
    {
        return $tab1['weight'] <=> $tab2['weight'];
    }


    /**
     * Returns an array(tab) descriptions
     *
     * @return array
     */
    protected function getTabs()
    {
        // Process tabs only once
        if ($this->processedTabs === null) {
            $this->processedTabs = [];
            $defaultValues = $this->getDefaultTabValues();

            foreach ($this->prepareTabs() as $key => $tab) {
                $target = $tab['url_params']['target'] ?? $key;
                $params = $tab['url_params'] ?? [];

                $references = $tab['references'] ?? [];

                $tab['selected'] = $this->isSelectedTab($target, $params) || $this->isSelectedTabByReference($references);

                $tab['url'] = $params
                    ? $this->buildURL($target, '', $params)
                    : $this->buildTabURL($target);

                $subTabs = [];

                if (isset($tab['subTabsWidget']) && class_exists($tab['subTabsWidget'])) {
                    $subTabsWidgetParams = $tab['subTabsWidgetParams'] ?? [];
                    $subTabsWidget = new $tab['subTabsWidget']($subTabsWidgetParams);
                    $subTabs = $subTabsWidget->getTabsForSubmenu();
                    if (!is_array($subTabs) && count($subTabs) < 2) {
                        $subTabs = [];
                    }
                }

                $tab['subTabs'] = $subTabs;

                // Set default values for missing tab parameters
                $tab += $defaultValues;

                $this->processedTabs[$key] = $tab;
            }
        }

        return $this->processedTabs;
    }

    /**
     * Get tabs data for
     *
     * @return array
     */
    public function getTabsForSubmenu()
    {
        return $this->getTabs();
    }

    /**
     * getTitle
     *
     * @return string
     */
    protected function getTitle()
    {
        return null;
    }

    /**
     * Returns a description of the selected tab. If no tab is selected, returns NULL.
     *
     * @return array
     */
    protected function getSelectedTab()
    {
        foreach ($this->getTabs() as $tab) {
            if ($tab['selected']) {
                return $tab;
            }
        }

        return null;
    }

    /**
     * Get tab link template
     *
     * @param array $tab Tab data
     *
     * @return bool|string
     */
    protected function getTabLinkTemplate(array $tab)
    {
        return false;
    }
}
