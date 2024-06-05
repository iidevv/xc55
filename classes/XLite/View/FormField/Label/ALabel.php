<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Label;

/**
 * ALabel
 */
abstract class ALabel extends \XLite\View\FormField\AFormField
{
    /**
     * Widget param names
     */
    public const PARAM_UNESCAPE = 'unescape';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
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
            static::PARAM_UNESCAPE => new \XLite\Model\WidgetParam\TypeBool('Un-escape value', false),
        ];
    }

    protected function isEscape()
    {
        return !$this->getParam(static::PARAM_UNESCAPE);
    }

    /**
     * Get label value
     *
     * @return string
     */
    protected function getLabelValue()
    {
        $value = strval($this->getValue());

        if ($this->isEscape()) {
            $value = func_htmlspecialchars($value);
        }

        return $value;
    }

    /**
     * Set the form field as "form control" (some major styling will be applied)
     *
     * @return boolean
     */
    protected function isFormControl()
    {
        return false;
    }
}
