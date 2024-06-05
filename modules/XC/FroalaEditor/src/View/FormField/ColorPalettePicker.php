<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FroalaEditor\View\FormField;

class ColorPalettePicker extends \XLite\View\FormField\Input\Text
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/FroalaEditor/form_field/input/colorPalette.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/FroalaEditor/form_field/input/style.less';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/FroalaEditor/form_field/';
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/body.twig';
    }

    /**
     * @return false|string
     */
    public function getDataJson()
    {
        $colors = explode(',', $this->getValue());

        $data = [
            'colors' => array_map(static function ($color) {
                return [ 'value' => $color ];
            }, $colors)
        ];

        return json_encode($data);
    }

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS] = array_merge($list[static::RESOURCE_JS], static::getVueLibraries());
        $list[static::RESOURCE_CSS][] = 'colorpicker/classic.min.css';
        $list[static::RESOURCE_CSS][] = 'colorpicker/style.less';
        $list[static::RESOURCE_JS][]  = 'colorpicker/pickr.min.js';
        $list[static::RESOURCE_JS][]  = 'colorpicker/extend.js';

        return $list;
    }

    /**
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 10000;
    }

    /**
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'color-palette';

        return $classes;
    }
}
