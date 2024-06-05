<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\View;

use XCart\Extender\Mapping\Extender;
use XC\WebmasterKit\Core\TemplatesDebugger;

/**
 * CommonResources widget
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (TemplatesDebugger::getInstance()->isMarkTemplatesEnabled()) {
            $list[static::RESOURCE_JS][] = 'modules/XC/WebmasterKit/template_debugger.js';
        }

        return $list;
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (TemplatesDebugger::getInstance()->isMarkTemplatesEnabled()) {
            $list[] = 'modules/XC/WebmasterKit/template_debugger.less';
        }

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/WebmasterKit/core.js';

        return $list;
    }
}
