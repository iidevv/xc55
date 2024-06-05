<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Price
 */
class Price extends \XLite\View\FormField\Input\Text\Symbol
{
    public const PARAM_CURRENCY = 'currency';
    public const PARAM_DASHED  = 'dashed';

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
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return floatval(parent::prepareRequestData($value));
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
     * Get currency symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        $result = $this->getSymbolType() === 'prefix'
            ? $this->getCurrency()->getPrefix()
            : $this->getCurrency()->getSuffix();

        return $result ?: $this->getCurrency()->getCode();
    }

    /**
     * Return symbol type
     *
     * @return string
     */
    public function getSymbolType()
    {
        return $this->getCurrency()->getSuffix() && !$this->getCurrency()->getPrefix()
            ? 'suffix'
            : 'prefix';
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
            self::PARAM_CURRENCY => new \XLite\Model\WidgetParam\TypeObject(
                'Currency',
                \XLite::getInstance()->getCurrency(),
                false,
                'XLite\Model\Currency'
            ),
            self::PARAM_DASHED  => new \XLite\Model\WidgetParam\TypeBool('Dash as empty value', false),
        ];
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
        $classes[] = 'price';

        return $classes;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();
        $attributes['value'] = $this->formatValue($attributes['value']);

        $attributes['data-dashed']  = $this->getParam(self::PARAM_DASHED);

        return $attributes;
    }

    /**
     * Format value
     *
     * @param float $value Value
     *
     * @return string
     */
    protected function formatValue($value)
    {
        return number_format(
            round($value, $this->getE()),
            $this->getE(),
            '.',
            ''
        );
    }

    /**
     * Get mantis
     *
     * @return integer
     */
    protected function getE()
    {
        return $this->getCurrency()->getE();
    }
}
