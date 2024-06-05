<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Name-value abstract storage
 *
 * @ORM\MappedSuperclass
 */
abstract class NameValue extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Parameter name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name;

    /**
     * Semi-serialized parameter value representation
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $value;

    /**
     * Get parameter value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = @unserialize($this->value);

        return $value === false ? $this->value : $value;
    }

    /**
     * Set parameter value
     *
     * @param mixed $value Parameter value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = is_scalar($value) ? $value : serialize($value);
    }
}
