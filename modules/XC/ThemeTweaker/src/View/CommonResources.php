<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XC\ThemeTweaker\Core\ThemeTweaker;
use XC\ThemeTweaker\Main as ThemeTweakerMain;

/**
 * @Extender\Mixin
 */
class CommonResources extends \XLite\View\CommonResources
{
    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        $schema   = parent::getResourcesSchema();
        $schema[] = ['getThemeTweakerCustomFiles', 1000, null, null];

        return $schema;
    }

    /**
     * Return custom common files
     *
     * @return array
     */
    protected function getThemeTweakerCustomFiles()
    {
        $files = [];

        if (!\XLite::isAdminZone()) {
            if ($this->isCustomJsEnabled()) {
                $files[static::RESOURCE_JS] = [
                    [
                        'file'  => 'var/theme/custom.js',
                        'media' => 'all',
                        'url'   => ThemeTweakerMain::getResourceURL(ThemeTweakerMain::getThemeDir() . 'custom.js'),
                    ],
                ];
            }

            if (
                $this->isCustomCssEnabled()
                && !ThemeTweaker::getInstance()->isInCustomCssMode()
            ) {
                $files[static::RESOURCE_CSS] = [
                    [
                        'file'  => LC_DIR_PUBLIC . 'var/theme/custom.css',
                        'media' => 'all',
                        'url'   => ThemeTweakerMain::getResourceURL(ThemeTweakerMain::getThemeDir() . 'custom.css'),
                    ],
                ];
            }
        }

        return $files;
    }

    protected function isCustomJsEnabled()
    {
        return ThemeTweaker::castCheckboxValue(
            Config::getInstance()->XC->ThemeTweaker->use_custom_js
        );
    }

    protected function isCustomCssEnabled()
    {
        return ThemeTweaker::castCheckboxValue(
            Config::getInstance()->XC->ThemeTweaker->use_custom_css
        );
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_CSS][] = 'froala-editor/css/froala_style.fixed.css';

        if (
            (ThemeTweaker::getInstance()->isInWebmasterMode() || ThemeTweaker::getInstance()->isInEmailTemplateMode())
            && \XLite::isAdminZone()
        ) {
            $list[static::RESOURCE_JS][]  = 'modules/XC/ThemeTweaker/template_editor/vakata-jstree/dist/jstree.min.js';
            $list[static::RESOURCE_JS][]  = 'modules/XC/ThemeTweaker/template_editor/tree-view.js';
            $list[static::RESOURCE_JS][]  = 'modules/XC/ThemeTweaker/template_editor/template-navigator.js';
            $list[static::RESOURCE_JS][]  = 'modules/XC/ThemeTweaker/template_editor/editor.js';
            $list[static::RESOURCE_CSS][] = 'modules/XC/ThemeTweaker/template_editor/vakata-jstree/dist/themes/default/style.min.css';
            $list[static::RESOURCE_CSS][] = 'modules/XC/ThemeTweaker/template_editor/style.css';
            $list[static::RESOURCE_CSS][] = 'modules/XC/ThemeTweaker/template_editor/template-navigator.css';
        }

        return $list;
    }
}
