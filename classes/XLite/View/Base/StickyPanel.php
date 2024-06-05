<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Base;

/**
 * Sticky panel
 */
abstract class StickyPanel extends \XLite\View\Container
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'js/stickyPanel.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/sticky_panel.twig';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return 'sticky-panel';
    }
}
