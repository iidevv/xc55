<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Special Offer model.
 *
 * It stores information on what special offer logic should be used and offer settings.
 *
 * @ORM\Entity (repositoryClass="\QSL\SpecialOffersBase\Model\Repo\SpecialOffer")
 * @ORM\Table  (name="special_offers",
 *      indexes={
 *          @ORM\Index (name="offer_id", columns={"offer_id"}),
 *          @ORM\Index (name="name", columns={"name"}),
 *          @ORM\Index (name="position", columns={"position"}),
 *          @ORM\Index (name="promoHome", columns={"promoHome"}),
 *          @ORM\Index (name="promoOffers", columns={"promoOffers"}),
 *          @ORM\Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class SpecialOffer extends \XLite\Model\Base\I18n
{
    /**
     * Unique identifier of the offer.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $offer_id;

    /**
     * Offer Type.
     *
     * @var \QSL\SpecialOffersBase\Model\OfferType
     *
     * @ORM\ManyToOne  (targetEntity="QSL\SpecialOffersBase\Model\OfferType", inversedBy="specialOffers")
     * @ORM\JoinColumn (name="type_id", referencedColumnName="type_id", onDelete="CASCADE")
     */
    protected $offerType; // when the offer type is deleted, the operation cascades to all realted offers (via SQL)

    /**
     * Administrative name of the special offer.
     *
     * @var string
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Position of the exit offer among other ones in the list.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Whether the offer is enabled, or not.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Date range (begin)
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $activeFrom = 0;

    /**
     * Date range (end)
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $activeTill = 0;

    /**
     * Identifiers of other special offers that this offer may not apply together on the same item.
     *
     * @var array
     *
     * @ORM\Column (type="array", nullable=true)
     */
    protected $exclusions = [];

    /**
     * One-to-one relation with special_offer_images table
     *
     * @var \QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image
     *
     * @ORM\OneToOne  (targetEntity="QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image", mappedBy="specialOffer", cascade={"all"})
     */
    protected $image;

    /**
     * Whether the short promo text and image is displayed on the home page, or not.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $promoHome = true;

    /**
     * Whether the short promo text and image is displayed on Special Offers page.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $promoOffers = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\SpecialOffersBase\Model\SpecialOfferTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get the model ID.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getBrandId();
    }

    /**
     * Check if this offer can apply on an item together with the specified offer.
     *
     * @param integer $otherOfferId Identifier of the other offer to check.
     *
     * @return boolean
     */
    public function canApplyTogether($otherOfferId)
    {
        return !(in_array($otherOfferId, $this->exclusions));
    }

    /**
     * Since Doctrine lifecycle callbacks do not allow to modify associations, we've added this method
     *
     * @param string $type Type of current operation
     *
     * @return void
     */
    public function prepareEntityBeforeCommit($type)
    {
        if ($type == static::ACTION_UPDATE && !$this->getOfferType()->hasAllRequiredClasses()) {
            $this->setEnabled(false);
        }

        parent::prepareEntityBeforeCommit($type);
    }

    /**
     * Returns the offer identifier.
     *
     * @return integer
     */
    public function getOfferId()
    {
        return $this->offer_id;
    }

    /**
     * Sets the administrative name for the offer type.
     *
     * @param string $name Administrative name
     *
     * @return SpecialOffer
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the administrative name for the offer type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the position of the offer among others.
     *
     * @param integer $position Position
     *
     * @return SpecialOffer
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Returns the position of the offer among others.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Confgiures whether the special offer is enabled, or disabled.
     *
     * @param boolean $enabled New state
     *
     * @return SpecialOffer
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Checks if the special offer is enabled, or not.
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Sets the date that the offer is active from (timestamp).
     *
     * @param integer $activeFrom Date (timestamp)
     *
     * @return SpecialOffer
     */
    public function setActiveFrom($activeFrom)
    {
        $this->activeFrom = intval($activeFrom);

        return $this;
    }

    /**
     * Returns the date that the offer is active from (timestamp).
     *
     * @return integer
     */
    public function getActiveFrom()
    {
        return $this->activeFrom;
    }

    /**
     * Configures the date that the offer is active till (timestamp).
     *
     * @param integer $activeTill Date (timestamp)
     *
     * @return SpecialOffer
     */
    public function setActiveTill($activeTill)
    {
        $this->activeTill = intval($activeTill);

        return $this;
    }

    /**
     * Returns the date that the offer is active till (timestamp).
     *
     * @return integer
     */
    public function getActiveTill()
    {
        return $this->activeTill;
    }

    /**
     * Configures the list of special offers that this one cannot be combined with.
     *
     * @param array $exclusions Special offers
     *
     * @return SpecialOffer
     */
    public function setExclusions($exclusions)
    {
        $this->exclusions = $exclusions;

        return $this;
    }

    /**
     * Returns the list of special offers that this one cannot be combined with.
     *
     * @return array
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }

    /**
     * Configures whether the promo should be displayed on the home page.
     *
     * @param boolean $promoHome State
     *
     * @return SpecialOffer
     */
    public function setPromoHome($promoHome)
    {
        $this->promoHome = $promoHome;

        return $this;
    }

    /**
     * Checks if the promo should be displayed on the home page.
     *
     * @return boolean
     */
    public function getPromoHome()
    {
        return $this->promoHome;
    }

    /**
     * Configures whether the promo should be displayed on the Special Offers page.
     *
     * @param boolean $promoOffers State
     *
     * @return SpecialOffer
     */
    public function setPromoOffers($promoOffers)
    {
        $this->promoOffers = $promoOffers;

        return $this;
    }

    /**
     * Checks if the promo should be displayed on the Special Offers page.
     *
     * @return boolean
     */
    public function getPromoOffers()
    {
        return $this->promoOffers;
    }

    /**
     * Sets the type for the special offer.
     *
     * @param \QSL\SpecialOffersBase\Model\OfferType $offerType Offer type
     *
     * @return SpecialOffer
     */
    public function setOfferType(\QSL\SpecialOffersBase\Model\OfferType $offerType = null)
    {
        $this->offerType = $offerType;
        return $this;
    }

    /**
     * Returns the type of the special offer.
     *
     * @return \QSL\SpecialOffersBase\Model\OfferType
     */
    public function getOfferType()
    {
        return $this->offerType;
    }

    /**
     * Associates the special offer with an image.
     *
     * @param \QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image $image
     *
     * @return SpecialOffer
     */
    public function setImage(\QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Returns the special offer image.
     *
     * @return \QSL\SpecialOffersBase\Model\Image\SpecialOffer\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $title
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setTitle($title)
    {
        return $this->setTranslationField(__FUNCTION__, $title);
    }

    /**
     * @return string
     */
    public function getShortPromoText()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $shortPromoText
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setShortPromoText($shortPromoText)
    {
        return $this->setTranslationField(__FUNCTION__, $shortPromoText);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }

    /**
     * @return string
     */
    public function getCartPromoText()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $cartPromoText
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCartPromoText($cartPromoText)
    {
        return $this->setTranslationField(__FUNCTION__, $cartPromoText);
    }

    /**
     * @return string
     */
    public function getCartAppliedText()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $cartAppliedText
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCartAppliedText($cartAppliedText)
    {
        return $this->setTranslationField(__FUNCTION__, $cartAppliedText);
    }

    // }}}
}
