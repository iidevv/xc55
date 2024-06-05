<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Session;
use XLite\Logic\Export\Generator;
use const PREG_SET_ORDER;

/**
 * Translation core routine
 *
 * @Extender\Mixin
 */
abstract class Translation extends \XLite\Core\Translation
{
    /**
     * Plural constants
     */
    const PLURAL_CHECK = '{{tp|';
    const PLURAL_REGEXP = '/{{tc\|(?P<choices>.+?)(?!}})\|(?P<number>[^|}]+)}}/';

    /**
     * Ordinal constants
     */
    const ORDINAL_CHECK = '{{to|';
    const ORDINAL_REGEXP = '/{{to\|(?P<choices>.+?)(?!}})\|(?P<number>[^|}]+)}}/';

    /**
     * Choose $forms[0] for singular $forms[1] for plural base on $number
     *
     * @param integer $number Number
     * @param array   $forms  Forms
     * @param string  $code   Language code
     *
     * @return string
     */
    protected static function selectPlural($number, $forms, $code)
    {
        $result = '';

        if ('en' == $code && 2 == count($forms)) {
            $result = $forms[1 == $number ? 0 : 1];
        }

        if ('ru' == $code && 3 == count($forms)) {
            $index = (($number % 10 === 1) && ($number % 100 !== 11))
                ? 0
                : (
                    ($number % 10 >= 2) && ($number % 10 <= 4)
                    && (($number % 100 < 10) || ($number % 100 >= 20))
                        ? 1 : 2
                );

            $result = $forms[$index];
        }

        return $result;
    }

    /**
     * Choose ordinal ending from $forms base on $number
     *
     * @param integer $number Number
     * @param array   $forms  Forms
     * @param string  $code   Language code
     *
     * @return string
     */
    protected static function selectOrdinal($number, $forms, $code)
    {
        $result = '';

        if ('en' == $code && 4 == count($forms)) {
            // number ends on 11, 12 or 13
            if ($number % 100 > 10 && $number % 100 < 14) {
                $result = $forms[3];
            } else {
                switch ($number % 10) {
                    case 1:
                        $result = $forms[0];
                        break;

                    case 2:
                        $result = $forms[1];
                        break;

                    case 3:
                        $result = $forms[2];
                        break;

                    default:
                        $result = $forms[3];
                }
            }
        }

        if ('ru' == $code && 3 == count($forms)) {
            // number ends on 11, 12 or 13
            if ($number % 100 > 10 && $number % 100 < 20) {
                $result = $forms[0];
            } else {
                switch ($number % 10) {
                    case 0:
                    case 1:
                    case 4:
                    case 5:
                    case 9:
                        $result = $forms[0];
                        break;

                    case 2:
                    case 6:
                    case 7:
                    case 8:
                    default:
                        $result = $forms[1];
                        break;

                    case 3:
                        $result = $forms[2];
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * Process choices
     *
     * @param string $checkString  Check string
     * @param string $regexpString Check string
     * @param string $string       Translated string
     * @param array  $arguments    Substitute arguments OPTIONAL
     * @param string $code         Language code OPTIONAL
     *
     * @return string
     */
    protected static function processChoices(
        $checkString,
        $regexpString,
        $string,
        array $arguments = [],
        $code = null
    ) {
        if (strpos($string, $checkString) !== false) {
            $matches = [];
            if (preg_match_all($regexpString, $string, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $choice = '';
                    if (isset($arguments[$match['number']])) {
                        $forms = explode('|', $match['choices']);
                        $choice = static::selectOrdinal($arguments[$match['number']], $forms, $code);
                    }

                    if ($choice) {
                        $string = str_replace($match[0], $choice, $string);
                    }
                }
            }
        }

        return $string;
    }

    /**
     * Process plural
     *
     * @param string $string    Translated string
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function processPlural($string, array $arguments = [], $code = null)
    {
        return static::processChoices(static::PLURAL_CHECK, static::PLURAL_REGEXP, $string, $arguments, $code);
    }

    /**
     * Process ordinal
     *
     * @param string $string    Translated string
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    protected static function processOrdinal($string, array $arguments = [], $code = null)
    {
        return static::processChoices(static::ORDINAL_CHECK, static::ORDINAL_REGEXP, $string, $arguments, $code);
    }

    /**
     * Translate plural
     *
     * @param string  $name      Label name
     * @param integer $number    Number
     * @param array   $arguments Substitute arguments OPTIONAL
     * @param string  $code      Language code OPTIONAL
     *
     * @return string
     */
    public function translatePlural($name, $number, array $arguments = [], $code = null)
    {
        $translated = $this->translate($name, $arguments, $code);
        $forms = explode('|', $translated);

        if (empty($code)) {
            $code = Generator::getLanguageCode()
                ?: Session::getInstance()->getLanguage()->getCode();
        }

        return static::selectPlural($number, $forms, $code);
    }

    /**
     * Translate ordinal
     *
     * @param string  $name      Label name
     * @param integer $number    Number
     * @param array   $arguments Substitute arguments OPTIONAL
     * @param string  $code      Language code OPTIONAL
     *
     * @return string
     */
    public function translateOrdinal($name, $number, array $arguments = [], $code = null)
    {
        $translated = $this->translate($name, $arguments, $code);
        $forms = explode('|', $translated);

        if (empty($code)) {
            $code = Generator::getLanguageCode()
                ?: Session::getInstance()->getLanguage()->getCode();
        }

        return static::selectOrdinal($number, $forms, $code);
    }

    /**
     * Translate by string
     *
     * @param string $name      Label name
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    public function translateByString($name, array $arguments = [], $code = null)
    {
        $result = parent::translateByString($name, $arguments, $code);

        if (empty($code)) {
            $code = Generator::getLanguageCode()
                ?: Session::getInstance()->getLanguage()->getCode();
        }

        $result = static::processPlural($result, $arguments, $code);
        return static::processOrdinal($result, $arguments, $code);
    }
}
