<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * View list container
 * @Extender\Mixin
 */
abstract class ListContainer extends \XLite\View\ListContainer implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    protected function getDefaultDisplayName()
    {
        return $this->getGroupName();
    }

    /**
     * Define view list item metadata
     *
     * @param \XLite\Model\ViewList $item ViewList item
     *
     * @return array
     */
    protected function getListItemMetadata($item)
    {
        return [
            'weight'     => $item->getWeightActual(),
            'list'       => $item->getListActual() ?: $item->getList(),
            'list_id'    => $item->getListId(),
            'visibility' => !$item->isHidden(),
            'entityId'   => $item->getEntityId(),
        ];
    }

    /**
     * Display view list content
     *
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return void
     */
    public function displayViewListContent($list, array $arguments = [])
    {
        $arguments = array_merge(
            $arguments,
            [
                \XC\ThemeTweaker\View\LayoutBlockInterface::PARAM_DISPLAY_GROUP => $this->getGroupName()
            ]
        );

        if ($this->isInLayoutMode()) {
            print "<div class='list-items-group' data-list='$list'>";
        }

        foreach ($this->getViewList($list, $arguments) as $widget) {
            $this->displayViewListItem($widget);
        }

        if ($this->isInLayoutMode()) {
            print '</div>';
        }
    }

    /**
     * Display view list content
     *
     * @param \XLite\View\AView $widget     Widget to display
     *
     * @return void
     */
    protected function displayViewListItem($widget)
    {
        if ($this->isInLayoutMode()) {
            $classes = $this->getViewListItemClasses($widget);
            $attrs = $this->getViewListItemAttributes($widget);
            print "<div class='$classes' $attrs>";
                print "<div class='list-item-actions'>";
                    parent::displayViewListContent('list-item.actions');
                print '</div>';
                print "<div class='list-item-content'>";
                    $widget->display();
                print '</div>';
            print '</div>';
        } else {
            $widget->display();
        }
    }

    /**
     * Return string with list item classes
     *
     * @param \XLite\View\AView $widget     Displaying widget
     *
     * @return string
     */
    protected function getViewListItemClasses($widget)
    {
        $metadata = $widget->getParam(static::PARAM_METADATA);
        $visibility = $metadata['visibility'] ? '' : 'list-item__hidden';

        return 'list-item ' . $visibility;
    }

    /**
     * Return string with list item attributes
     *
     * @param \XLite\View\AView $widget     Displaying widget
     *
     * @return string
     */
    protected function getViewListItemAttributes($widget)
    {
        $result = '';
        foreach ($this->defineViewListItemAttributes($widget) as $key => $value) {
            $result .= " data-$key='$value'";
        }

        return $result;
    }

    /**
     * Define list item attributes
     *
     * @param \XLite\View\AView $widget     Displaying widget
     *
     * @return array
     */
    protected function defineViewListItemAttributes($widget)
    {
        $attrs = [
            'widget' => $widget->originalClassName ?? get_class($widget),
        ];

        $metadata = $widget->getParam(static::PARAM_METADATA);
        if ($metadata) {
            $attrs['weight'] = $metadata['weight'] ?: 0;
            $attrs['id']     = $metadata['list_id'] ?: 0;
            $attrs['list']   = $metadata['list'];
        }

        $displayGroup = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_DISPLAY_GROUP);
        if ($displayGroup) {
            $attrs['display'] = $displayGroup;
        }

        $displayName = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_DISPLAY_NAME);
        if ($displayName) {
            $attrs['blockName'] = $displayName;
        }

        $settingsLink = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_LAYOUT_SETTINGS_LINK);
        if ($settingsLink) {
            $attrs['settings-link'] = $settingsLink;
        }

        $helpMessage = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_LAYOUT_HELP_MESSAGE);
        if ($helpMessage) {
            $attrs['help-message'] = $helpMessage;
        }

        $bodyEntityId = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_LAYOUT_BODY_ENTITY_ID);
        if ($bodyEntityId) {
            $attrs['bodyEntityId'] = $bodyEntityId;
        }

        $removeId = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_LAYOUT_REMOVE_ID);
        if ($removeId) {
            $attrs['removeId'] = $removeId;
        }

        $lazyLoad = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_LAYOUT_LAZY_LOAD);
        if ($lazyLoad) {
            $attrs['lazyLoad'] = $lazyLoad;
        }

        $isReloadedWidget = $widget->getParam(ThemeTweaker\View\LayoutBlockInterface::PARAM_IS_RELOADED_WIDGET);
        if ($isReloadedWidget) {
            $attrs['is-reloaded-widget'] = $isReloadedWidget;
        }

        return $attrs;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/XC/ThemeTweaker/list_container/list_container.css'
            ]
        );
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'modules/XC/ThemeTweaker/list_container/jquery.listItem.js',
                'modules/XC/ThemeTweaker/list_container/list_container.js',
            ]
        );
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][]    = 'js/Sortable.js';

        return $list;
    }
}
