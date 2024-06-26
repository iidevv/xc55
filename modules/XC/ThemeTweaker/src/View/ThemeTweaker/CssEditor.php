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
class CssEditor extends \XC\ThemeTweaker\View\Custom
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/custom_css';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/css_editor.twig';
    }

    public function isVisible()
    {
        return \XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->isInCustomCssMode();
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/css_editor.js';

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
        $list[] = $this->getDir() . '/css_editor_style.css';

        return $list;
    }

    /**
     * Return code mode
     *
     * @return string
     */
    protected function getCodeMode()
    {
        return 'css';
    }

    protected function getCustomCss()
    {
        return \Includes\Utils\FileManager::read($this->getCustomCssPath());
    }

    protected function getCustomCssPath()
    {
        return \XC\ThemeTweaker\Main::getThemeDir() . 'custom.css';
    }

    /**
     * Code is used or not
     *
     * @return boolean
     */
    protected function isUsed()
    {
        return (bool) \XLite\Core\Config::getInstance()->XC->ThemeTweaker->{'use_' . $this->getType()};
    }
}
