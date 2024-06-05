<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Base;

abstract class SuperClass
{
    /**
     * @var string
     */
    protected static $defaultLanguage = \XLite\Core\Translation::DEFAULT_LANGUAGE;

    /**
     * @var int
     */
    protected static $userTime;

    /**
     * @param string $code
     */
    public static function setDefaultLanguage($code)
    {
        static::$defaultLanguage = $code;
    }

    /**
     * @return string
     */
    public static function getDefaultLanguage()
    {
        return static::$defaultLanguage;
    }

    /**
     * Return converted into user time current timestamp
     *
     * @return int
     */
    public static function getUserTime()
    {
        if (!isset(static::$userTime)) {
            static::$userTime = \XLite\Core\Converter::convertTimeToUser();
        }
        return static::$userTime;
    }

    /**
     * Language label translation short method
     *
     * @param string $name      Label name
     * @param array  $arguments Substitution arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     * @param string $type      Label type, can be used in \XLite\Core\ITranslationProcessor
     *
     * @return string
     */
    protected static function t($name, array $arguments = [], $code = null, $type = null)
    {
        return \XLite\Core\Translation::lbl($name, $arguments, $code, $type);
    }

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @return void
     */
    protected function __construct()
    {
    }
}
