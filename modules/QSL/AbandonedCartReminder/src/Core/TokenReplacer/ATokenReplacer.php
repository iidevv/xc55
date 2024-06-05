<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Core\TokenReplacer;

/**
 * Provides methods to replace tokens in a string.
 *
 */
abstract class ATokenReplacer extends \XLite\Base
{
    /**
     * Cached replacement strings.
     *
     * @var array
     */
    protected $cachedStrings = [];

    /**
     * Return list of allowed tokens.
     *
     * @return array
     */
    public static function getAllowedTokens()
    {
        return [];
    }

    /**
     * Constructor.
     *
     * @param array $params Constructor parameters OPTIONAL
     *
     * @return \QSL\AbandonedCartReminder\Core\TokenReplacer\ATokenReplacer
     */
    public function __construct(array $params = [])
    {
        $this->setParams($params);
    }

    /**
     * Parse the string and replace tokens with their values.
     *
     * @param string $string String containing tokens that should be parsed
     *
     * @return string
     */
    public function replaceTokens($string)
    {
        $tokens = [];

        foreach (static::getAllowedTokens() as $name) {
            $key = $this->getTokenKey($name);
            if (is_numeric(strpos($string, $key))) {
                $tokens[$key] = $this->getTokenString($name);
            }
        }

        return strtr($string, $tokens);
    }

    /**
     * Set input parameters by executing setter methods.
     *
     * @param array $params Parameters
     *
     * @return void
     */
    protected function setParams(array $params)
    {
        foreach ($params as $name => $value) {
            $method = $this->getSetterMethod($name);
            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $value);
            }
        }
    }

    /**
     * Return name of the setter method for the parameter.
     *
     * @param string $paramName Parameter name.
     *
     * @return string
     */
    protected function getSetterMethod($paramName)
    {
        return preg_match('/^[a-z_]\w+$/i', $paramName)
            ? 'set' . (str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($paramName)))))
            : '';
    }

    /**
     * Check whether it is a valid token name.
     *
     * @param string $name Token name
     *
     * @return boolean
     */
    protected function isValidTokenName($name)
    {
        return preg_match('/^[_a-zA-Z0-9]+$/', $name);
    }

    /**
     * Return token key that should be searched for.
     *
     * @param string $name Token name
     *
     * @return string
     */
    protected function getTokenKey($name)
    {
        return '[' . strtoupper($name) . ']';
    }

    /**
     * Return name of the method to get the token replacement string.
     *
     * @param string $name Token name
     *
     * @return string
     */
    protected function getTokenMethod($name)
    {
        return 'getTokenString' . (str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($name)))));
    }

    /**
     * Return token value by executing a method responsible for the token.
     *
     * @param string $name Token name
     *
     * @return string
     */
    protected function getTokenString($name)
    {
        $key = $this->getTokenKey($name);

        if (!isset($this->cachedStrings[$key])) {
            $string = '';
            if ($this->isValidTokenName($name)) {
                $method = $this->getTokenMethod($name);
                if (method_exists($this, $method)) {
                    $string = call_user_func([$this, $method]);
                }
            }
            $this->cachedStrings[$key] = $string;
        }

        return $this->cachedStrings[$key];
    }
}
