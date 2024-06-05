<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\View\Layout\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Layout;

/**
 * Sidebar first list collection container
 *
 * @Extender\Mixin
 */
class SidebarFirst extends \XLite\View\Layout\Customer\SidebarFirst
{
    public function displayInnerContent()
    {
        ob_start();

        parent::displayInnerContent();

        $content = ob_get_contents();

        ob_end_clean();

        $layout = Layout::getInstance();

        $layout->setCloudSearchSidebarContent($content);

        echo $content;
    }
}