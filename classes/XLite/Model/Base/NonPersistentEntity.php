<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

/**
 * NonPersistentEntity
 */
abstract class NonPersistentEntity extends \XLite\Base\SuperClass
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return true if specified property exists
     *
     * @param string $name Property name
     *
     * @return boolean
     */
    public function isPropertyExists($name)
    {
        return property_exists($this, $name) || property_exists($this, \Includes\Utils\Converter::convertFromCamelCase($name));
    }

    /**
     * Map data to entity
     *
     * @param array $data Data
     *
     * @return $this
     */
    public function map(array $data)
    {
        foreach ($data as $key => $value) {
            // Map only existing properties with setter methods or direct
            $method = 'set' . \Includes\Utils\Converter::convertToUpperCamelCase($key);

            if (method_exists($this, $method)) {
                // $method is assembled from 'set' + getMethodName()
                $this->$method($value);
            } else {
                $this->setterProperty($key, $value);
            }
        }

        return $this;
    }

    /**
     * Universal setter
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return true|null Returns TRUE if the setting succeeds. NULL if the setting fails
     */
    public function setterProperty($property, $value)
    {
        $result = property_exists($this, $property);

        if ($result) {
            // Get property value
            $this->$property = $value;
        }

        return $result ?: null;
    }

    /**
     * Update entity
     *
     * @return boolean
     */
    public function update()
    {
        return true;
    }

    /**
     * Create entity
     *
     * @return boolean
     */
    public function create()
    {
        return $this->update();
    }

    /**
     * Delete entity
     *
     * @return boolean
     */
    public function delete()
    {
        return true;
    }
}
