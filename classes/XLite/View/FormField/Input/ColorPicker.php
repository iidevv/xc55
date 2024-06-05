<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

use XLite\Model\WidgetParam\TypeString;

class ColorPicker extends \XLite\View\FormField\Input\AInput
{
    public const PARAM_AVAILABLE_COLORS = 'availableColors';

    /**
     * Getter for Field-only flag
     *
     * @return boolean
     */
    protected function getDefaultParamFieldOnly()
    {
        return true;
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_AVAILABLE_COLORS => new TypeString('Available colors', ''),
        ];
    }

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_CSS][] = 'colorpicker/classic.min.css';
        $list[static::RESOURCE_CSS][] = 'colorpicker/style.less';
        $list[static::RESOURCE_JS][]  = 'colorpicker/pickr.min.js';
        $list[static::RESOURCE_JS][]  = 'colorpicker/script.js';
        $list[static::RESOURCE_JS][]  = 'colorpicker/extend.js';

        return $list;
    }

    /**
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'color-picker-field';

        return $classes;
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_HIDDEN;
    }

    /**
     * @return array
     */
    protected function getCommentedData()
    {
        return parent::getCommentedData() + [
                'save'  => static::t('Save'),
                'clear' => static::t('Clear'),
                'availableColors' => $this->getColors()
            ];
    }

    /**
     * @return array
     */
    protected function getColors()
    {
        $availableColors = $this->getParam(self::PARAM_AVAILABLE_COLORS);

        $availableColors = $availableColors ? explode(',', $availableColors) : [];

        return array_map(static function ($v) {
            return '#' . $v;
        }, $availableColors);
    }

    /**
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 6;
    }
}
