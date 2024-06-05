<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Layout\Customer;

/**
 * Header bottom list collection container
 */
class LayoutWideBottom extends \XLite\View\ListContainer
{
    /**
     * Return string with list item classes
     *
     * @param \XLite\View\AView $widget Displaying widget
     *
     * @return string
     */
    protected function getViewListItemClasses($widget)
    {
        $classes = parent::getViewListItemClasses($widget);

        return $classes . ' list-item__wide-bottom';
    }

    /**
     * Displays inner content
     */
    public function displayInnerContent()
    {
        $content = parent::displayInnerContent();
        echo $content;
    }

    /**
     * @return string
     */
    protected function getDefaultInnerList()
    {
        return 'layout.bottom.wide';
    }
}
