<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Address field value (additional fields) model
 *
 * @ORM\Entity
 * @ORM\Table (name="address_field_value")
 */
class AddressFieldValue extends \XLite\Model\AEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", nullable=false)
     */
    protected $id;

    /**
     * Additional field value
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $value = '';

    /**
     * Address field model relation
     *
     * @var \XLite\Model\AddressField
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\AddressField", cascade={"persist","merge","detach"})
     * @ORM\JoinColumn (name="address_field_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $addressField;

    /**
     * Address model relation
     *
     * @var \XLite\Model\Address
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Address", inversedBy="addressFields", cascade={"persist","merge","detach"})
     * @ORM\JoinColumn (name="address_id", referencedColumnName="address_id", onDelete="CASCADE")
     */
    protected $address;

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
     * Set value
     *
     * @param string $value
     *
     * @return AddressFieldValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set addressField
     *
     * @param \XLite\Model\AddressField $addressField
     *
     * @return AddressFieldValue
     */
    public function setAddressField(\XLite\Model\AddressField $addressField = null)
    {
        $this->addressField = $addressField;

        return $this;
    }

    /**
     * Get addressField
     *
     * @return \XLite\Model\AddressField
     */
    public function getAddressField()
    {
        return $this->addressField;
    }

    /**
     * Set address
     *
     * @param \XLite\Model\Address $address
     *
     * @return AddressFieldValue
     */
    public function setAddress(\XLite\Model\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \XLite\Model\Address
     */
    public function getAddress()
    {
        return $this->address;
    }
}
