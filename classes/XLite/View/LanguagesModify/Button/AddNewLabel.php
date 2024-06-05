<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify\Button;

/**
 * Add new label button
 */
class AddNewLabel extends \XLite\View\Button\Regular
{
    /**
     * Widget parameters
     */
    public const PARAM_LANGUAGE = 'label-language';
    public const PARAM_PAGE = 'page';
    public const PARAM_SECTION = 'section';
    public const DEFAULT_SECTION = 'design';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_LANGUAGE    => new \XLite\Model\WidgetParam\TypeString(
                'Language code',
                \XLite\Core\Request::getInstance()->code ?: static::getDefaultLanguage()
            ),
            self::PARAM_PAGE        => new \XLite\Model\WidgetParam\TypeInt('Page index', 1),
            self::PARAM_SECTION     => new \XLite\Model\WidgetParam\TypeString(
                'Section index',
                \XLite\Core\Request::getInstance()->section ?: self::DEFAULT_SECTION
            ),
        ];
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Add new label';
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'add-new-label';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return 'openAddNewLabel(this, '
            . '\'' . $this->getParam(self::PARAM_LANGUAGE) . '\', '
            . '\'' . $this->getParam(self::PARAM_SECTION) . '\');';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' always-reload');
    }
}
