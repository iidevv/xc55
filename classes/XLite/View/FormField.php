<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Form field widget
 */
class FormField extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    public const PARAM_FIELD = 'field';


    /**
     * Used in form field components to display a form field according to the 'field' property
     * FIXME - to check
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getParam(static::PARAM_FIELD);
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getParam(static::PARAM_FIELD);
    }

    /**
     * Return a value for the "id" attribute of the field input tag
     *
     * @return string
     */
    public function getFieldId()
    {
        return strtolower(strtr($this->getName()));
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
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
            static::PARAM_FIELD => new \XLite\Model\WidgetParam\TypeString('Field', null),
        ];
    }
}
