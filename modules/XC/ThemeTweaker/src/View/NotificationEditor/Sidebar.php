<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\NotificationEditor;

use XCart\Extender\Mapping\ListChild;

/**
 * Sidebar
 *
 * @ListChild (list="admin.main.page.content.center", zone="admin")
 */
class Sidebar extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/body.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/ThemeTweaker/notification_editor/style.css',
        ]);
    }


    public static function getAllowedTargets()
    {
        return [
            'notification_editor',
        ];
    }
}
