<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Layout manager
 *
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    /*
     * Store sidebar content so we can correctly change sidebar state after materializing FiltersBoxPlaceholder -> FiltersBox widget
     */
    protected $cloudSearchSidebarContent;

    /**
     * @return string
     */
    public function getCloudSearchSidebarContent()
    {
        return $this->cloudSearchSidebarContent;
    }

    /**
     * @param string $content
     */
    public function setCloudSearchSidebarContent($content)
    {
        $this->cloudSearchSidebarContent = $content;
    }
}
