<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\DeliveryService;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class represents a Canada Post delivery service's option
 *
 * @ORM\Entity
 * @ORM\Table  (name="capost_delivery_service_options")
 */
class Option extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Option code
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=false)
     */
    protected $code;

    /**
     * Option name
     * TODO: remove that field and make getting an option name by a function
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * Indicates whether this option is mandatory for the service
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $mandatory = false;

    /**
     * True indicates that this option if selected must include a qualifier on the option.
     * This is true for insurance (COV) and collect on delivery (COD) options
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $qualifierRequired = false;

    /**
     * Numeric – indicates the maximum value of the qualifier for this service.
     * The maximum value of a qualifier may differ between services. This is specific to the insurance (COV) option.
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4, nullable=true)
     */
    protected $qualifierMax = 0.0000;

    /**
     * Item's service (reference to the item's service model)
     *
     * @var \XC\CanadaPost\Model\DeliveryService
     *
     * @ORM\ManyToOne  (targetEntity="XC\CanadaPost\Model\DeliveryService", inversedBy="options")
     * @ORM\JoinColumn (name="serviceId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $service;

    // {{{ Service methods

    /**
     * Assign the service
     *
     * @param \XC\CanadaPost\Model\DeliveryService $service Item's service model (OPTIONAL)
     *
     * @return void
     */
    public function setService(\XC\CanadaPost\Model\DeliveryService $service = null)
    {
        $this->service = $service;
    }

    // }}}

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
     * Set code
     *
     * @param string $code
     * @return Option
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Option
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mandatory
     *
     * @param boolean $mandatory
     * @return Option
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;
        return $this;
    }

    /**
     * Get mandatory
     *
     * @return boolean
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set qualifierRequired
     *
     * @param boolean $qualifierRequired
     * @return Option
     */
    public function setQualifierRequired($qualifierRequired)
    {
        $this->qualifierRequired = $qualifierRequired;
        return $this;
    }

    /**
     * Get qualifierRequired
     *
     * @return boolean
     */
    public function getQualifierRequired()
    {
        return $this->qualifierRequired;
    }

    /**
     * Set qualifierMax
     *
     * @param float $qualifierMax
     * @return Option
     */
    public function setQualifierMax($qualifierMax)
    {
        $this->qualifierMax = $qualifierMax;
        return $this;
    }

    /**
     * Get qualifierMax
     *
     * @return float
     */
    public function getQualifierMax()
    {
        return $this->qualifierMax;
    }

    /**
     * Get service
     *
     * @return \XC\CanadaPost\Model\DeliveryService
     */
    public function getService()
    {
        return $this->service;
    }
}
