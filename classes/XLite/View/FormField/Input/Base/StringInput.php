<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Base;

/**
 * String-based
 */
abstract class StringInput extends \XLite\View\FormField\Input\AInput
{
    /**
     * Widget param names
     */
    public const PARAM_DEFAULT_VALUE = 'defaultValue';
    public const PARAM_MAX_LENGTH    = 'maxlength';
    public const PARAM_MIN_LENGTH    = 'minlength';
    public const PARAM_AUTOCOMPLETE  = 'autocomplete';
    public const PARAM_READ_ONLY     = 'readOnly';

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        $value = parent::prepareRequestData($value);

        return mb_substr($value, 0, $this->getParam(static::PARAM_MAX_LENGTH));
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
            static::PARAM_DEFAULT_VALUE => new \XLite\Model\WidgetParam\TypeString('Default value', ''),
            static::PARAM_MAX_LENGTH    => new \XLite\Model\WidgetParam\TypeInt('Maximum length', $this->getDefaultMaxSize()),
            static::PARAM_MIN_LENGTH    => new \XLite\Model\WidgetParam\TypeInt('Minimum length', 0),
            static::PARAM_AUTOCOMPLETE  => new \XLite\Model\WidgetParam\TypeBool('Autocomplete', false),
            static::PARAM_READ_ONLY     => new \XLite\Model\WidgetParam\TypeBool('Read Only', false),
        ];
    }

    /**
     * Use autocomplete or not
     *
     * @return boolean
     */
    protected function isUseAutocomplete()
    {
        return $this->getParam(static::PARAM_AUTOCOMPLETE);
    }

    /**
     * Whether the field is read-only.
     */
    protected function isReadOnly(): bool
    {
        return (bool) $this->getParam(static::PARAM_READ_ONLY);
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['autocomplete'] = $this->isUseAutocomplete() ? 'on' : 'off';
        if ($this->isReadOnly()) {
            $list['readonly'] = 'readonly';
        }

        if ($this->getParam(static::PARAM_MAX_LENGTH)) {
            $list['maxlength'] = $this->getParam(static::PARAM_MAX_LENGTH);
        }

        return $list;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 255;
    }

    /**
     * Check field validity
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $result = parent::checkFieldValidity();

        if ($result && strlen($result) > $this->getParam(self::PARAM_MAX_LENGTH)) {
            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field should not be longer than Y',
                [
                    'name' => $this->getLabel(),
                    'max'  => $this->getParam(self::PARAM_MAX_LENGTH),
                ]
            );
        }
        if ($result && strlen($result) < $this->getParam(self::PARAM_MIN_LENGTH)) {
            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value of the X field should not be shorter than Y',
                [
                    'name' => $this->getLabel(),
                    'min'  => $this->getParam(self::PARAM_MIN_LENGTH),
                ]
            );
        }

        return $result;
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        $rules[] = 'maxSize[' . $this->getParam(self::PARAM_MAX_LENGTH) . ']';

        $minLength = $this->getParam(self::PARAM_MIN_LENGTH);
        if ($minLength > 0) {
            $rules[] = 'minSize[' . $minLength . ']';
        }

        return $rules;
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return [
            'defaultValue' => $this->getParam(self::PARAM_DEFAULT_VALUE),
        ];
    }

    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitize()
    {
        return trim(parent::sanitize());
    }
}
