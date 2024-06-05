<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\FormField\Input\Text;

/**
 * Input field for the setting that determines the maximum discount which a shopper
 * can get for an order by redeeming his reward points.
 */
class RewardDiscountCap extends \XLite\View\FormField\Input\Text
{
    /**
     * Widget param names.
     */
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
        $list   = parent::getJSFiles();
        $list[] = 'modules/QSL/LoyaltyProgram/form_field/input/text/reward_discount_cap.js';

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
     * Define widget params.
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_E                  => new \XLite\Model\WidgetParam\TypeInt(
                'Number of digits after the decimal separator',
                2
            ),
            static::PARAM_THOUSAND_SEPARATOR => new \XLite\Model\WidgetParam\TypeString(
                'Thousand separator',
                \XLite\Core\Config::getInstance()->Units->thousand_delim
            ),
            static::PARAM_DECIMAL_SEPARATOR  => new \XLite\Model\WidgetParam\TypeString(
                'Decimal separator',
                \XLite\Core\Config::getInstance()->Units->decimal_delim
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
     * @param string $value Value entered by the user.
     *
     * @return mixed
     */
    protected function sanitizeValue($value)
    {
        $matches = $this->matchValue($value);

        if ($matches) {
            $percent = $matches[3];
            $amount  = $percent ? $matches[2] : round(doubleval($matches[2]), $this->getParam(self::PARAM_E));
            $value   = $amount . $percent;
        } else {
            $value = '';
        }

        return $value;
    }

    /**
     * Match a value to the discount field format.
     *
     * @param string $value
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
        $rules   = parent::assembleValidationRules();
        $rules[] = 'funcCall[checkRewardDiscountCap]';

        return $rules;
    }

    /**
     * Assemble classes.
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes   = parent::assembleClasses($classes);
        $classes[] = 'reward-discount-cap';

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

        $attributes['data-invalid-message'] = static::t('Enter either a fixed sum or a percent value.');

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
        $parentIsValid   = parent::checkFieldValidity();
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
            $result             = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value should be either a fixed sum or a percent value',
                [
                    'name' => $this->getLabel(),
                ]
            );
        } elseif ($matches && $matches[1] === '-') {
            $result             = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The value must be greater than zero',
                [
                    'name' => $this->getLabel(),
                ]
            );
        } elseif ($matches && ($matches[3] === '%') && ($matches[2] > 100)) {
            $result             = false;
            $this->errorMessage = \XLite\Core\Translation::lbl(
                'The discount cannot be greater than 100%',
                [
                    'name' => $this->getLabel(),
                ]
            );
        }

        return $result;
    }
}
