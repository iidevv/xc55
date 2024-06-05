<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\FormField\Textarea;

/**
 * Textarea
 */
class CodeMirror extends \XLite\View\FormField\Textarea\Simple
{
    /**
     * Widget param names
     */
    public const PARAM_CODE_MODE = 'codeMode';

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_CSS][] = [
            'file'      => 'modules/XC/ThemeTweaker/codemirror/lib/codemirror.css',
            'no_minify' => true,
        ];

        $list[static::RESOURCE_CSS][] = 'modules/XC/ThemeTweaker/form_field/codemirror.css';

        $list[static::RESOURCE_JS][] = [
            'file'      => 'modules/XC/ThemeTweaker/codemirror/lib/min/codemirror.js',
            'no_minify' => true,
        ];

        $list[static::RESOURCE_JS][] = 'modules/XC/ThemeTweaker/form_field/codemirror.js';
        $list[static::RESOURCE_JS][] = 'modules/XC/ThemeTweaker/codemirror/addon/display/placeholder.js';

        $mode = $this->getParam(static::PARAM_CODE_MODE);

        if ($mode) {
            $list[static::RESOURCE_JS][] = sprintf('modules/XC/ThemeTweaker/codemirror/mode/%s/%s.js', $mode, $mode);
        }

        return $list;
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CODE_MODE  => new \XLite\Model\WidgetParam\TypeString('Mode', ''),
        ];
    }

    /**
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes   = parent::assembleClasses($classes);
        $classes[] = 'codemirror';

        if (!\XLite::getController()->isAJAX()) {
            $classes[] = 'autoloadable';
        }

        return $classes;
    }

    /**
     * @return array
     */
    protected function getAttributes()
    {
        $attributes = parent::getAttributes();
        $attributes['data-codemirror-mode'] = $this->getParam(static::PARAM_CODE_MODE);

        return $attributes;
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return \XLite::isAdminZone()
            ? 'modules/XC/ThemeTweaker/form_field/code_mirror/body.twig'
            : parent::getFieldTemplate();
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return \XLite::isAdminZone() ? '' : parent::getDir();
    }
}
