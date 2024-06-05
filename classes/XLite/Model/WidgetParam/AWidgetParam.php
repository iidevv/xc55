<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\WidgetParam;

/**
 * ____description____
 */
abstract class AWidgetParam extends \XLite\Base\SuperClass implements \Serializable
{
    /**
     * Indexes in the "conditions" array
     */
    public const ATTR_CONDITION = 'condition';
    public const ATTR_MESSAGE   = 'text';
    public const ATTR_CONTINUE  = 'continue';

    /**
     * Param type
     *
     * @var string
     */
    protected $type = null;

    /**
     * Param value
     *
     * @var mixed
     */
    protected $value = null;

    /**
     * Param label
     *
     * @var string
     */
    protected $label = null;

    /**
     * Determines if the param will be diaplayed in CMS as widget setting
     *
     * @var mixed
     */
    protected $isSetting = false;

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->type,
            $this->value,
            $this->label,
            $this->isSetting
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        [
            $this->type,
            $this->value,
            $this->label,
            $this->isSetting
            ] = unserialize($serialized);
    }

    /**
     * Return list of conditions to check
     *
     * @param mixed $value Value to validate
     *
     * @return array
     */
    abstract protected function getValidationSchema($value);

    /**
     * Constructor
     *
     * @param mixed $label     Param label (text)
     * @param mixed $value     Default value OPTIONAL
     * @param mixed $isSetting Display this setting in CMS or not OPTIONAL
     *
     * @return void
     */
    public function __construct($label, $value = null, $isSetting = false)
    {
        $this->label     = $label;
        $this->isSetting = $isSetting;

        $this->setValue($value);
    }

    /**
     * Validate passed value
     *
     * @param mixed $value Value to validate
     *
     * @return mixed
     */
    public function validate($value)
    {
        $result = $this->checkConditions($this->getValidationSchema($value));

        return [empty($result), $result];
    }

    /**
     * Return protected property
     *
     * @param string $name Property name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name ?? null;
    }

    /**
     * Set param value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Append data to param value
     *
     * @param mixed $value Value to append
     *
     * @return void
     */
    public function appendValue($value)
    {
        $this->value += $value;
    }

    /**
     * setVisibility
     *
     * @param boolean $isSetting Visibility flag
     *
     * @return void
     */
    public function setVisibility($isSetting)
    {
        $this->isSetting = $isSetting;
    }


    /**
     * Check passed conditions
     *
     * @param array $conditions Conditions to check
     *
     * @return array
     */
    protected function checkConditions(array $conditions)
    {
        $messages = [];

        foreach ($conditions as $condition) {
            if ($condition[static::ATTR_CONDITION] === true) {
                $messages[] = $condition[static::ATTR_MESSAGE];

                if (!isset($condition[static::ATTR_CONTINUE])) {
                     break;
                }
            }
        }

        return $messages;
    }
}
