<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\NotificationEditor\Sidebar;

class Tree extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/tree/body.twig';
    }

    /**
     * @return string
     */
    protected function getTreeContent()
    {
        $viewer = \XLite::getController()->getViewer();
        return $viewer::getHtmlTree();
    }

    protected function getInterface()
    {
        return \XLite::INTERFACE_MAIL;
    }

    protected function getZone()
    {
        return \XLite\Core\Request::getInstance()->zone;
    }
}
