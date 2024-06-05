<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Layout\Customer;

use XLite\InjectLoggerTrait;

/**
 * Sidebar first list collection container
 */
class SidebarFirst extends \XLite\View\ListContainer
{
    use InjectLoggerTrait;

    public function displayInnerContent()
    {
        $content = '';

        if ($this->getInnerList()) {
            $content = $this->getViewListContent($this->getInnerList());
        } elseif ($this->getInnerTemplate()) {
            $content = $this->getContent();
        } else {
            $this->getLogger()->error('No list or template was given to ListContainer');
        }

        $sidebarState = \XLite\Core\Layout::getInstance()->getSidebarState();
        if ($this->isEmptyContent($content)) {
            $sidebarState |= \XLite\Core\Layout::SIDEBAR_STATE_FIRST_EMPTY;
            \XLite\Core\Layout::getInstance()->setSidebarState($sidebarState);
        }

        echo $content;
    }

    /**
     * @param $content
     *
     * @return bool
     */
    protected function isEmptyContent($content)
    {
        return empty($content);
    }

    /**
     * There are two modes of sidebars:
     *   single_sidebar
     *     We are displaying both viewlists in one sidebar
     *   two_sidebar
     *     We are displaying viewlists independently in SidebarFirst and SidebarSecond
     *
     * @return string
     */
    protected function getDefaultInnerList()
    {
        return \XLite\Core\Layout::getInstance()->isSidebarSingle()
            ? 'sidebar.first,sidebar.second'
            : 'sidebar.first';
    }

    /**
     * @param string $list      List name
     * @param array  $arguments List common arguments OPTIONAL
     *
     * @return \XLite\View\AView[]
     */
    protected function getViewList($list, array $arguments = [])
    {
        $result = parent::getViewList($list, $arguments);

        $sidebarState = \XLite\Core\Layout::getInstance()->getSidebarState();
        if (count($result) === 1 && $result[0] instanceof \XLite\View\TopCategories) {
            $sidebarState |= \XLite\Core\Layout::SIDEBAR_STATE_FIRST_ONLY_CATEGORIES;
            \XLite\Core\Layout::getInstance()->setSidebarState($sidebarState);
        }

        return $result;
    }
}
