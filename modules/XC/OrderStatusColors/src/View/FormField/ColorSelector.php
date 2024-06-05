<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\View\FormField;

use XLite\Model\WidgetParam\TypeString;
use XLite\View\FormField\Input\Text;

class ColorSelector extends Text
{
    /**
     * Widget param names
     */
    public const PARAM_AVAILABLE_COLORS = 'availableColors';

    /*
     * Availabel colors
     *
     * @var array
     */
    protected $colors;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/OrderStatusColors/form_field/color_selector/script.js';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_AVAILABLE_COLORS => new TypeString('Available colors', ''),
        ];
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/OrderStatusColors/form_field/color_selector/style.css';

        return $list;
    }

    protected function getDir()
    {
        return 'modules/XC/OrderStatusColors/form_field/';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'color_selector/body.twig';
    }

    /**
     * Return avaialable colors for selection
     *
     * @return array
     */
    protected function getColors()
    {
        if (!isset($this->colors)) {
            $this->colors = explode(',', $this->getParam(self::PARAM_AVAILABLE_COLORS));
        }
        return $this->colors;
    }
}
