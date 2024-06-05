<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ThemeTweaker;

use XCart\Extender\Mapping\ListChild;

/**
 * Code widget
 *
 * @ListChild (list="themetweaker-panel--content", weight="100")
 */
class LabelsEditor extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/labels_editor';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/popover-template.js';
        $list[] = $this->getDir() . '/editable-label.js';
        $list[] = $this->getDir() . '/labels_editor_panel.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/editable-label.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/labels_editor.twig';
    }

    public function isVisible()
    {
        return \XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->isInLabelsMode();
    }
}
