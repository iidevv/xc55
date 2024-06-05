<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LayoutSettings;

use XLite\Core\Layout;

/**
 * Layout settings
 */
class LayoutTypeSelector extends \XLite\View\AView
{
    /**
     * Returns styles
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            'layout_settings/parts/layout_settings.type_selector.css'
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout_settings/parts/layout_settings.type_selector.twig';
    }

    protected function getLayoutGroups()
    {
        return Layout::getInstance()->getAvailableLayoutTypes();
    }

    protected function getLayoutTypeLabel($group)
    {
        return Layout::getInstance()->getLayoutTypeLabelByGroup($group);
    }

    protected function getLayoutType($group)
    {
        return Layout::getInstance()->getLayoutType($group);
    }

    protected function getLayoutTypes($group)
    {
        $types = Layout::getInstance()->getAvailableLayoutTypes();

        return $types[$group] ?? [];
    }
}
