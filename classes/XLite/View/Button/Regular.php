<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Regular button
 */
class Regular extends \XLite\View\Button\AButton
{
    /**
     * Widget parameter names
     */
    public const PARAM_ACTION      = 'action';
    public const PARAM_JS_CODE     = 'jsCode';
    public const PARAM_FORM_PARAMS = 'formParams';

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return null;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ACTION      => new \XLite\Model\WidgetParam\TypeString('LC action', $this->getDefaultAction(), true),
            self::PARAM_JS_CODE     => new \XLite\Model\WidgetParam\TypeString('JS code', '', true),
            self::PARAM_FORM_PARAMS => new \XLite\Model\WidgetParam\TypeCollection('Form params to modify', [], true),
        ];
    }

    /**
     * JavaScript: compose the associative array definition by PHP array
     *
     * @param array $params Values to compose
     *
     * @return string
     */
    protected function getJSFormParams(array $params)
    {
        $result = [];

        foreach ($params as $name => $value) {
            $result[] = $name . ': \'' . $value . '\'';
        }

        return implode(',', $result);
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        $formParams = $this->getParam(self::PARAM_FORM_PARAMS);

        if (!isset($formParams['action']) && $this->getParam(self::PARAM_ACTION)) {
            $formParams['action'] = $this->getParam(self::PARAM_ACTION);
        }

        return 'if (!jQuery(this).hasClass(\'disabled\')) '
            . ($formParams
                ? 'submitForm(this.form, {' . $this->getJSFormParams($formParams) . '})'
                : 'submitFormDefault(this.form);'
            );
    }

    /**
     * Return specified (or default) JS code
     *
     * @return string
     */
    protected function getJSCode()
    {
        $jsCode = $this->getParam(self::PARAM_JS_CODE);

        return empty($jsCode) ? $this->getDefaultJSCode() : $jsCode;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();

        $list['onclick'] = 'javascript: ' . $this->getJSCode();

        return $list;
    }
}
