<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

abstract class Converter extends \Includes\Utils\AUtils
{
    /**
     * File size suffixes.
     * Source: http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
     * Source: http://physics.nist.gov/cuu/Units/binary.html
     *
     * @var array
     */
    protected static $byteMultipliers = ['b', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

    /**
     * Remove leading characters from string
     *
     * @param string $string string to prepare
     * @param string $chars  charlist to remove
     *
     * @return string
     */
    public static function trimLeadingChars($string, $chars)
    {
        return ltrim($string, $chars);
    }

    /**
     * Remove trailing characters from string
     *
     * @param string $string string to prepare
     * @param string $chars  charlist to remove
     *
     * @return string
     */
    public static function trimTrailingChars($string, $chars)
    {
        return rtrim($string, $chars);
    }

    /**
     * Get formatted price
     *
     * @param float $price value to format
     *
     * @return string
     */
    public static function formatPrice($price)
    {
        return sprintf('%.02f', round(doubleval($price), 2));
    }

    /**
     * @deprecated use \Includes\Utils\Converter::convertToUpperCamelCase
     */
    public static function convertToCamelCase($string)
    {
        return self::convertToUpperCamelCase($string);
    }

    /**
     * Convert a string like "test_foo_bar" into the camel case (like "testFooBar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToLowerCamelCase($string)
    {
        return lcfirst(self::convertToUpperCamelCase($string));
    }

    /**
     * Convert a string like "testFooBar" into the underline style (like "test_foo_bar")
     *
     * @param string $string    String to convert
     * @param string $delimiter Delimiter symbol
     *
     * @return string
     */
    public static function convertFromCamelCase($string, $delimiter = '_')
    {
        $string = preg_replace('/([A-Z])/', $delimiter . '$1', lcfirst((string) $string));

        return strtolower($string);
    }

    /**
     * @deprecated use \Includes\Utils\Converter::convertToUpperCamelCase
     */
    public static function convertToPascalCase($string)
    {
        return self::convertToUpperCamelCase($string);
    }

    /**
     * Convert a string like "test_foo_bar" or "test-foo-bar" or "test.foo.bar" into the Pascal case (like "TestFooBar")
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToUpperCamelCase($string)
    {
        $string = ucwords(str_replace(['-', '_', '.'], ' ', (string) $string));

        return str_replace(' ', '', $string);
    }

    /**
     * Prepare human-readable output for file size
     *
     * @param integer $size      Size in bytes
     * @param string  $separator To return a string OPTIONAL
     *
     * @return string
     */
    public static function formatFileSize($size, $separator = null)
    {
        $multiplier = 0;

        while (1000 < $size) {
            // http://en.wikipedia.org/wiki/Template:Quantities_of_bytes
            // http://physics.nist.gov/cuu/Units/binary.html
            $size /= 1000;

            $multiplier++;
        }

        // Do not display numbers after decimal point if size is in kilobytes.
        // When size is greater than display one number after decimal point.
        $result = [number_format($size, $multiplier > 1 ? 1 : 0), static::$byteMultipliers[$multiplier]];

        return isset($separator) ? implode($separator, $result) : $result;
    }

    /**
     * Remove \r and \n chars from string (e.g to prevent CRLF injections)
     *
     * @param string $value Input value
     *
     * @return string
     */
    public static function removeCRLF($value)
    {
        return trim(preg_replace('/[\r\n]+/', '', ((string)$value)));
    }

    /**
     * Compose URL from target, action and additional params
     *
     * @param string $target    Page identifier OPTIONAL
     * @param string $action    Action to perform OPTIONAL
     * @param array  $params    Additional params OPTIONAL
     * @param string $interface Interface script OPTIONAL
     *
     * @return string
     */
    public static function buildURL($target = '', $action = '', array $params = [], $interface = null)
    {
        $result = strval($interface);
        $result = $result === \XLite::getAdminScript() ? 'admin/' : '';
        $urlParams = [];

        if (!empty($target)) {
            $urlParams['target'] = $target;
        }

        if (!empty($action)) {
            $urlParams['action'] = $action;
        }

        $params = $urlParams + $params;

        if (!empty($params)) {
            $result .= '?' . http_build_query($params, '', '&');
        }

        return $result;
    }

    /**
     * Convert a string like "testFooBar" to translit
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public static function convertToTranslit($string)
    {
        return $string ? \URLify::transliterate($string) : '';
    }
}
