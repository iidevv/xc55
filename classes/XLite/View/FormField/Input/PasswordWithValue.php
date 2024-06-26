<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Password
 */
class PasswordWithValue extends \XLite\View\FormField\Input\Secure
{
    public const PLACEHOLDER_SIGN = 037;
    public const PLACEHOLDER_LENGTH = 8;

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return strpos($value, chr(static::PLACEHOLDER_SIGN)) !== false
            ? null
            : parent::prepareRequestData($value);
    }

    /**
     * setCommonAttributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $result = parent::setCommonAttributes($attrs);
        $result['value'] = strlen($result['value'])
            ? static::getPasswordValuePlaceholder()
            : '';

        return $result;
    }

    /**
     * @return string
     */
    public static function getPasswordValuePlaceholder()
    {
        return str_repeat(chr(static::PLACEHOLDER_SIGN), static::PLACEHOLDER_LENGTH);
    }

    /**
     * Return true if value is trusted (purification must be ignored)
     *
     * @return boolean
     */
    public function isTrusted()
    {
        return true;
    }
}
