<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\Form;

class NotificationEditorSidebarData extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'notification_editor';
    }

    protected function getDefaultAction()
    {
        return 'change_data';
    }

    protected function getDefaultParams()
    {
        $params = [
            'templatesDirectory' => \XLite\Core\Request::getInstance()->templatesDirectory,
            'zone'               => \XLite\Core\Request::getInstance()->zone,
        ];

        return $params;
    }
}
