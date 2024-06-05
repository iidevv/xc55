<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Special Offer Type model.
 *
 * It stores information on what special offer logic should be used and offer settings.
 *
 * @ORM\Entity (repositoryClass="\QSL\SpecialOffersBase\Model\Repo\OfferType")
 * @ORM\Table  (name="special_offer_types",
 *      indexes={
 *          @ORM\Index (name="type_id", columns={"type_id"}),
 *          @ORM\Index (name="processorClass", columns={"processorClass"}),
 *          @ORM\Index (name="position", columns={"position"}),
 *          @ORM\Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class OfferType extends \XLite\Model\Base\I18n
{
    /**
     * Unique identifier of the offer type.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $type_id;

    /**
     * Whether the offer type is enabled, or not.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Name of the class that implements the offer logic.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $processorClass;

    /**
     * Name of the class that implements the View Model logic.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $viewModelClass;

    /**
     * Position of the exit offer among other ones in the list.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Special offers of this type.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\SpecialOffersBase\Model\SpecialOffer", mappedBy="offerType", cascade={"remove"})
     */
    protected $specialOffers; // when the offer type is deleted, the operation cascades to all related offers (via Doctrine)

    /**
     * Cached processor class instance.
     *
     * @var \QSL\SpecialOffersBase\Logic\Order\SpecialOffer\ASpecialOffer
     */
    protected $processor;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\SpecialOffersBase\Model\OfferTypeTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Checks if the special offer has correct processor and view model classes.
     *
     * @return boolean
     */
    public function hasAllRequiredClasses()
    {
        return class_exists($this->getProcessorClass())
            && class_exists($this->getViewModelClass());
    }

    /**
     * Returns the processor class for this special offer type.
     *
     * @return \QSL\SpecialOffersBase\Logic\Order\SpecialOffer\ASpecialOffer
     */
    public function getProcessor()
    {
        if (!isset($this->processor)) {
            $this->processor = $this->factoryProcessor();
        }

        return $this->processor;
    }

    /**
     * Creates a new instance of the processor class for this special offer type.
     *
     * @return \QSL\SpecialOffersBase\Logic\Order\SpecialOffer\ASpecialOffer
     */
    protected function factoryProcessor()
    {
        $class = $this->getProcessorClass();

        return class_exists($class) ? new $class() : null;
    }

    /**
     * Returns the model identifier.
     *
     * @return integer
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Confgiures whether the special offer type is enabled, or disabled.
     *
     * @param boolean $enabled New state
     *
     * @return OfferType
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Checks if the special offer type is enabled, or not.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Updates the name of the processor class for the special offer.
     *
     * @param string $processorClass Class name
     *
     * @return OfferType
     */
    public function setProcessorClass($processorClass)
    {
        $this->processorClass = $processorClass;

        return $this;
    }

    /**
     * Returns the name of the processor class for the special offer type.
     *
     * @return string
     */
    public function getProcessorClass()
    {
        return $this->processorClass;
    }

    /**
     * Updates the name of the view class for the special offer type.
     *
     * @param string $viewModelClass Class name
     *
     * @return OfferType
     */
    public function setViewModelClass($viewModelClass)
    {
        $this->viewModelClass = $viewModelClass;

        return $this;
    }

    /**
     * Returns the name of the view class for the special offer type.
     *
     * @return string
     */
    public function getViewModelClass()
    {
        return $this->viewModelClass;
    }

    /**
     * Updates the position of the special offer type among others.
     *
     * @param integer $position New position
     *
     * @return OfferType
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Returns the position of the special offer type among others.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Associates a special offer with the special offer type.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $specialOffers Special offer model
     *
     * @return OfferType
     */
    public function addSpecialOffers(\QSL\SpecialOffersBase\Model\SpecialOffer $specialOffers)
    {
        $this->specialOffers[] = $specialOffers;

        return $this;
    }

    /**
     * Returns special offers of the type.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpecialOffers()
    {
        return $this->specialOffers;
    }
}
