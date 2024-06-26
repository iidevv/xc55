<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Float format selector
 */
class FloatFormat extends \XLite\View\FormField\Select\Regular
{
    /**
     * Parts of number
     */
    public const THOUSAND_PART     = '1';
    public const HUNDRENDS_PART    = '234';
    public const DECIMAL_PART      = '9';

    /**
     * Parameters name for a widget
     */
    public const PARAM_E = 'param_exp';

    /**
     * Exp. part replace element in format
     */
    public const FORMAT_EXP = 'e';

    /**
     * Delimiter to exclude the separators
     */
    public const FORMAT_DELIMITER = '|';

    /**
     * Pairs of formats
     *
     * @var array
     */
    protected $formatPairs = [
        [' ', '.'],
        [',', '.'],
        [' ', ','],
        ['.', ','],
        ['', '.'],
    ];

    /**
     * Return thousand and decimal delimiters array for a given format
     *
     * @param string $format
     *
     * @return array
     */
    public static function getDelimiters($format)
    {
        return explode(static::FORMAT_DELIMITER, $format, 2);
    }

    /**
     * Return a format for thousand, decimal delimiters
     *
     * @param string $thousandDelimiter
     * @param string $decimalDelimiter
     *
     * @return string
     */
    public static function getFormat($thousandDelimiter, $decimalDelimiter)
    {
        return $thousandDelimiter . static::FORMAT_DELIMITER . $decimalDelimiter;
    }

    /**
     * Return formatted element string
     *
     * @param array $elem
     *
     * @return string
     */
    public function formatElement($elem)
    {
        return static::THOUSAND_PART . $elem[0] . static::HUNDRENDS_PART
            . ($this->getE() == 0 ? '' : $elem[1] . str_repeat(static::DECIMAL_PART, $this->getE()));
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

        foreach ($this->getWidgetParams() as $name => $param) {
            if ($name == static::PARAM_OPTIONS) {
                $param->setValue($this->getFormatOptions());
                break;
            }
        }
    }

    /**
     * Get format pairs array with the key
     *
     * @return array
     */
    protected function getPairs()
    {
        $pairs = [];

        foreach ($this->formatPairs as $pair) {
            $pairs[$pair[0] . static::FORMAT_DELIMITER . $pair[1]] = $pair;
        }

        return $pairs;
    }

    /**
     * Return exp. part number for a selector
     *
     * @return integer
     */
    protected function getE()
    {
        return $this->getParam(static::PARAM_E);
    }

    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
    }

    /**
     * Get options list
     *
     * @return array
     */
    protected function getFormatOptions()
    {
        return array_unique(
            array_map(
                [$this, 'formatElement'],
                $this->getPairs()
            )
        );
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
            static::PARAM_E => new \XLite\Model\WidgetParam\TypeInt('Exp part', 4),
        ];
    }
}
