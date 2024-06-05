<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * ZoneElement model
 *
 * @ORM\Entity
 * @ORM\Table (name="zone_elements",
 *      indexes={
 *          @ORM\Index (name="type_value", columns={"element_type","element_value"}),
 *          @ORM\Index (name="id_type", columns={"zone_id","element_type"})
 *      }
 * )
 */
class ZoneElement extends \XLite\Model\AEntity
{
    /*
     * Zone element types
     */
    public const ZONE_ELEMENT_COUNTRY = 'C';
    public const ZONE_ELEMENT_STATE   = 'S';
    public const ZONE_ELEMENT_TOWN    = 'T';
    public const ZONE_ELEMENT_ZIPCODE = 'Z';
    public const ZONE_ELEMENT_ADDRESS = 'A';

    /**
     * Unique zone element Id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", length=11, nullable=false)
     */
    protected $element_id;

    /**
     * Zone element value, e.g. 'US', 'US_NY', 'New Y%' etc
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $element_value;

    /**
     * Element type
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $element_type;

    /**
     * Zone (relation)
     *
     * @var \XLite\Model\Zone
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Zone", inversedBy="zone_elements")
     * @ORM\JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")
     */
    protected $zone;

    /**
     * getElementTypesData
     *
     * @return array
     */
    public static function getElementTypesData()
    {
        return [
            static::ZONE_ELEMENT_COUNTRY => [
                'field'      => 'country',   // Address field name
                'weight'     => 0x01,        // Element weight
                'funcSuffix' => 'Countries', // Suffix for functions name: getZone<Suffix>, checkZone<Suffix>
                'required'   => true,        // Required property: if true then entire zone declined if this element does bot match
            ],
            static::ZONE_ELEMENT_STATE   => [
                'field'      => 'state',
                'weight'     => 0x02,
                'funcSuffix' => 'States',
                'required'   => true,
            ],
            static::ZONE_ELEMENT_ZIPCODE => [
                'field'      => 'zipcode',
                'weight'     => 0x08,
                'funcSuffix' => 'ZipCodes',
                'required'   => true,
            ],
            static::ZONE_ELEMENT_TOWN    => [
                'field'      => 'city',
                'weight'     => 0x10,
                'funcSuffix' => 'Cities',
                'required'   => true,
            ],
            static::ZONE_ELEMENT_ADDRESS => [
                'field'      => 'address',
                'weight'     => 0x20,
                'funcSuffix' => 'Addresses',
                'required'   => false,
            ]
        ];
    }

    /**
     * Get element_id
     *
     * @return integer
     */
    public function getElementId()
    {
        return $this->element_id;
    }

    /**
     * Set element_value
     *
     * @param string $elementValue
     * @return ZoneElement
     */
    public function setElementValue($elementValue)
    {
        $this->element_value = $elementValue;
        return $this;
    }

    /**
     * Get element_value
     *
     * @return string
     */
    public function getElementValue()
    {
        return $this->element_value;
    }

    /**
     * Set element_type
     *
     * @param string $elementType
     * @return ZoneElement
     */
    public function setElementType($elementType)
    {
        $this->element_type = $elementType;
        return $this;
    }

    /**
     * Get element_type
     *
     * @return string
     */
    public function getElementType()
    {
        return $this->element_type;
    }

    /**
     * Set zone
     *
     * @param \XLite\Model\Zone $zone
     * @return ZoneElement
     */
    public function setZone(\XLite\Model\Zone $zone = null)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * Get zone
     *
     * @return \XLite\Model\Zone
     */
    public function getZone()
    {
        return $this->zone;
    }
}
