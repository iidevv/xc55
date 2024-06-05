<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Switch
 */
class Switcher extends \XLite\View\FormField\Input\Checkbox
{
    public const PARAM_SWITCHER_ICON = 'switcherIcon';
    public const PARAM_SWITCHER_OFF_LABEL = 'offLabel';
    public const PARAM_SWITCHER_ON_LABEL = 'onLabel';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SWITCHER_ICON => new \XLite\Model\WidgetParam\TypeString('Switcher icon', 'fa-power-off iconfont'),
            self::PARAM_SWITCHER_OFF_LABEL => new \XLite\Model\WidgetParam\TypeString('Switcher disabled label', 'Disabled'),
            self::PARAM_SWITCHER_ON_LABEL => new \XLite\Model\WidgetParam\TypeString('Switcher enabled label', 'Enabled'),
        ];
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/checkbox/switcher.less';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            $this->getWidgetJSFiles()
        );
    }

    /**
     * Register CSS class to use for wrapper block (SPAN) of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return trim(
            parent::getWrapperClass() .
            ' switcher ' . $this->getTypeSwitcherClass() .
            ' ' . ($this->isChecked() ? 'enabled' : 'disabled')
        );
    }

    /**
     * Defines the specific switcher JS file
     *
     * @return array
     */
    protected function getWidgetJSFiles()
    {
        return [
            $this->getDir() . '/input/checkbox/switcher.js',
        ];
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return $this->getValue() || $this->checkSavedValue();
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/checkbox/switcher.twig';
    }

    /**
     * Define the specific CSS class for according the switcher type
     *
     * @return string
     */
    protected function getTypeSwitcherClass()
    {
        return 'switcher-read-write';
    }

    /**
     * Get widget title
     *
     * @return string
     */
    protected function getWidgetTitle()
    {
        return $this->isChecked() ? $this->getEnabledLabel() : $this->getDisabledLabel();
    }

    /**
     * Get widget icon
     *
     * @return string
     */
    protected function getIcon()
    {
        return $this->getParam(self::PARAM_SWITCHER_ICON);
    }

    /**
     * Get 'Disable' label
     *
     * @return string
     */
    protected function getDisabledLabel()
    {
        return $this->getParam(self::PARAM_SWITCHER_OFF_LABEL);
    }

    /**
     * Get 'Enable' label
     *
     * @return string
     */
    protected function getEnabledLabel()
    {
        return $this->getParam(self::PARAM_SWITCHER_ON_LABEL);
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['value'] = '1';

        return $list;
    }
}
