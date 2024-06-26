<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\DataStructure;

/**
 * Common cell
 *
 * @package XLite
 */
class Cell
{
    /**
     * Array of properties
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Constructor
     *
     * @param array $data data to set
     *
     * @return void
     */
    public function __construct(array $data = null)
    {
        !isset($data) ?: $this->setData($data);
    }

    /**
     * Get property by name
     *
     * @param string $name property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->properties[$name] ?? null;
    }

    /**
     * Set property value
     *
     * @param string $name  property name
     * @param mixed  $value property value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Check if property exists
     *
     * @param string $name property name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * Unset property
     *
     * @param string $name property name
     *
     * @return void
     */
    public function __unset($name)
    {
        unset($this->properties[$name]);
    }

    /**
     * Return all properties
     *
     * @return array
     */
    public function getData()
    {
        return $this->properties;
    }

    /**
     * Append data
     *
     * @param array $data data to set
     *
     * @return void
     */
    public function setData(array $data)
    {
        $this->properties = $data + $this->properties;
    }

    /**
     * Is empty data
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->properties);
    }
}
