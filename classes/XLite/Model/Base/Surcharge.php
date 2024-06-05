<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Surcharge
 *
 * @ORM\MappedSuperclass
 */
abstract class Surcharge extends \XLite\Model\AEntity
{
    /**
     * Surcharge type codes
     */
    public const TYPE_TAX      = 'tax';
    public const TYPE_DISCOUNT = 'discount';
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_HANDLING = 'handling';


    /**
     * Type names
     *
     * @var array
     */
    protected static $typeNames = [
        self::TYPE_TAX      => 'Tax cost',
        self::TYPE_DISCOUNT => 'Discount',
        self::TYPE_SHIPPING => 'Shipping cost',
        self::TYPE_HANDLING => 'Handling cost',
    ];

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
     * Type
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=8)
     */
    protected $type;

    /**
     * Code
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $code;

    /**
     * Control class name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $class;

    /**
     * Surcharge include flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $include = false;

    /**
     * Surcharge evailability
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $available = true;

    /**
     * Value
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $value;

    /**
     * Name (stored)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Weight
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $weight = 0;

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    abstract public function getOrder();

    /**
     * Set owner
     *
     * @param \XLite\Model\Base\SurchargeOwner $owner Owner
     *
     * @return \XLite\Model\Base\Surcharge
     */
    public function setOwner(\XLite\Model\Base\SurchargeOwner $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get unque surcharge key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->getType() . $this->getClass() . $this->name;
    }

    /**
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    public function getModifier()
    {
        $found = null;

        if ($this->getOrder()) {
            foreach ($this->getOrder()->getModifiers() as $modifier) {
                if ($modifier->isSurchargeOwner($this)) {
                    $found = $modifier;
                    break;
                }
            }
        }

        return $found;
    }

    /**
     * Get surcharge info
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    public function getInfo()
    {
        $modifier = $this->getModifier();

        return $modifier
            ? $modifier->getSurchargeInfo($this)
            : null;
    }

    /**
     * Get saved surcharge name
     *
     * @return string
     */
    public function getSurchargeName()
    {
        return $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        $info = $this->getInfo();

        return $info && $info->name ? $info->name : $this->name;
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getTypeName()
    {
        return isset(static::$typeNames[$this->getType()])
            ? \XLite\Core\Translation::getInstance()->translate(static::$typeNames[$this->getType()])
            : null;
    }

    /**
     * Set value
     *
     * @param float $value Value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = round($value, \XLite\Logic\Math::STORE_PRECISION);
    }

    /**
     * Check - current and specified surcharges are equal or not
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Another surcharge
     *
     * @return boolean
     */
    public function isEqualSurcharge(\XLite\Model\Base\Surcharge $surcharge)
    {
        return $this->getCode() === $surcharge->getCode()
            && $this->getType() === $surcharge->getType()
            && $this->getInclude() === $surcharge->getInclude()
            && $this->getClass() === $surcharge->getClass();
    }

    /**
     * Replace surcharge
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge for replacing
     *
     * @return void
     */
    public function replaceSurcharge(\XLite\Model\Base\Surcharge $surcharge)
    {
        $this->map($surcharge->getReplacedProperties());

        $owner = $surcharge->getOwner();

        $owner->removeSurcharge($surcharge);
        if ($surcharge->isPersistent()) {
            \XLite\Core\Database::getEM()->remove($surcharge);
        }

        $owner->addSurcharges($this);
        $this->setOwner($owner);
    }

    /**
     * Get replaced properties
     *
     * @return array
     */
    public function getReplacedProperties()
    {
        return [
            'value'     => $this->getValue(),
            'available' => $this->getAvailable(),
        ];
    }

    /**
     * Returns display sorting weight
     *
     * @return integer
     */
    public function getSortingWeight()
    {
        return $this->getModifier() !== null
            ? $this->getModifier()->getSortingWeight()
            : $this->weight;
    }
}
