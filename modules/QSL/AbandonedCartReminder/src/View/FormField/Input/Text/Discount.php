<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\FormField\Input\Text;

/**
 * Field for editing the coupon amount (nullable numeric followed with an optional % character).
 */
class Discount extends \XLite\View\FormField\Input\Text
{
    /**
     * Widget param names.
     */
    public const PARAM_CURRENCY           = 'currency';
    public const PARAM_E                  = 'e';
    public const PARAM_THOUSAND_SEPARATOR = 'thousand_separator';
    public const PARAM_DECIMAL_SEPARATOR  = 'decimal_separator';

    /**
     * Register JS files.
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/AbandonedCartReminder/form_field/input/text/discount.js';

        return $list;
    }

    /**
     * Get value.
     *
     * @return float
     */
    public function getValue()
    {
        return $this->sanitizeValue(parent::getValue());
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return $this->getParam(static::PARAM_CURRENCY);
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $currency = $this->getCurrency();
        foreach ($this->getWidgetParams() as $name => $param) {
            if ($name == static::PARAM_E) {
                $param->setValue($currency->getE());
            } elseif ($name == static::PARAM_THOUSAND_SEPARATOR) {
                $param->setValue($currency->getThousandDelimiter());
            } elseif ($name == static::PARAM_DECIMAL_SEPARATOR) {
                $param->setValue($currency->getDecimalDelimiter());
            }
        }
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
            static::PARAM_E => new \XLite\Model\WidgetParam\TypeInt(
                'Number of digits after the decimal separator',
                2
            ),
            static::PARAM_THOUSAND_SEPARATOR => new \XLite\Model\WidgetParam\TypeString(
                'Thousand separator',
                \XLite\Core\Config::getInstance()->Units->thousand_delim
            ),
            static::PARAM_DECIMAL_SEPARATOR => new \XLite\Model\WidgetParam\TypeString(
                'Decimal separator',
                \XLite\Core\Config::getInstance()->Units->decimal_delim
            ),
            self::PARAM_CURRENCY => new \XLite\Model\WidgetParam\TypeObject(
                'Currency',
                \XLite::getInstance()->getCurrency(),
                false,
                'XLite\Model\Currency'
            ),
        ];
    }

    /**
     * Sanitize value.
     *
     * @return mixed
     */
    protected function sanitize()
    {
        return $this->sanitizeValue(parent::sanitize());
    }

    /**
     * Sanitize value.
     *
     * @param string $value Field value.
     *
     * @return string
     */
    protected function sanitizeValue($value)
    {
        $matches = $this->matchValue($value);

        if ($matches) {
            $percent = $matches[3];
            $amount = $percent ? $matches[2] : round(doubleval($matches[2]), $this->getParam(self::PARAM_E));
            $value = $amount . $percent;
        } else {
            $value = '';
        }

        return $value;
    }

    /**
     * Match a value to the discount field format.
     *
     * @param string $value Field value
     *
     * @return array|boolean
     */
    protected function matchValue($value)
    {
        preg_match('/^([-+])?(\d+\.?\d*) *(%?)$/', trim($value), $matches);

        return $matches;
    }


    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();
        $rules[] = 'funcCall[checkDiscountAmount]';

        return $rules;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'discount';

        return $classes;
    }

    /**
     * Get default maximum size.
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 15;
    }

    /**
     * getCommonAttributes.
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();

        $attributes['data-decimal-delim']  = $this->getParam(self::PARAM_DECIMAL_SEPARATOR);
        $attributes['data-thousand-delim'] = $this->getParam(self::PARAM_THOUSAND_SEPARATOR);

        $e = $this->getE();
        if (isset($e)) {
            $attributes['data-e'] = $e;
        }

        $attributes['data-invalid-message'] = static::t('Enter fixed or percent discount.');

        return $attributes;
    }

    /**
     * Get mantis.
     *
     * @return integer
     */
    protected function getE()
    {
        return $this->getParam(static::PARAM_E);
    }

    /**
     * Check field validity.
     *
     * @return boolean
     */
    protected function checkFieldValidity()
    {
        $parentIsValid = parent::checkFieldValidity();
        $discountIsValid = $this->checkDiscountValidity();

        return $parentIsValid && $discountIsValid;
    }

    /**
     * Check whether the discount is valid .
     *
     * @return boolean
     */
    protected function checkDiscountValidity()
    {
        $result = true;

        $matches = $this->matchValue($this->getValue());

        if (!$matches && $this->getValue()) {
            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value should be a number followed with an optional percent sign',
                [
                    'name' => $this->getLabel(),
                ]
            );
        } elseif ($matches && ($matches[1] == '-')) {
            $result = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value cannot be negative',
                [
                    'name' => $this->getLabel(),
                ]
            );
        }

        return $result;
    }
}
