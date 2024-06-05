<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order modifier
 *
 * @ORM\Entity
 * @ORM\Table (name="order_modifiers")
 */
class Modifier extends \XLite\Model\AEntity
{
    /**
     * ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Logic class name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $class;

    /**
     * Weight
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $weight = 0;

    /**
     * Modifier object (cache)
     *
     * @var \XLite\Logic\Order\Modifier\AModifier
     */
    protected $modifier;

    /**
     * Magic call
     *
     * @param string $method Method name
     * @param array  $args   Arguments list OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        $modifier = $this->getModifier();

        return ($modifier && method_exists($modifier, $method))
            ? call_user_func_array([$modifier, $method], $args)
            : parent::__call($method, $args);
    }

    /**
     * Get modifier object
     *
     * @return \XLite\Logic\Order\Modifier\AModifier
     */
    public function getModifier()
    {
        if (!isset($this->modifier) && class_exists($this->getClass())) {
            $class = $this->getClass();
            $this->modifier = new $class($this);
        }

        return $this->modifier;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return Modifier
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Modifier
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }
}
