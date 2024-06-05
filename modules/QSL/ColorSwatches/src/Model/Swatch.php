<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name="qsl_color_swatches")
 */
class Swatch extends \XLite\Model\Base\I18n
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
     * Color
     *
     * @var string
     *
     * @ORM\Column (type="string", length=6, nullable=true)
     */
    protected $color;

    /**
     * Position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Image
     *
     * @var \QSL\ColorSwatches\Model\Image\Swatch
     *
     * @ORM\OneToOne (targetEntity="QSL\ColorSwatches\Model\Image\Swatch", mappedBy="swatch", cascade={"all"})
     */
    protected $image;

    /**
     * Attribute values
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueSelect", mappedBy="swatch", cascade={"all"})
     */
    protected $attributes;

    /**
     * Default flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $defaultValue = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\ColorSwatches\Model\SwatchTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @inheritdoc
     */
    public function __construct(array $data = [])
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
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
     * Set color
     *
     * @param string $color
     * @return Swatch
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Swatch
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set image
     *
     * @param \QSL\ColorSwatches\Model\Image\Swatch $image
     * @return Swatch
     */
    public function setImage(\QSL\ColorSwatches\Model\Image\Swatch $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return \QSL\ColorSwatches\Model\Image\Swatch
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add attributes
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $attributes
     * @return Swatch
     */
    public function addAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $attributes)
    {
        $this->attributes[] = $attributes;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param boolean $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return boolean
     */
    public function isDefaultValue()
    {
        return $this->getDefaultValue();
    }
}
