<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Special offers multilingual data
 *
 * @ORM\Entity
 *
 * @ORM\Table (name="special_offer_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"}),
 *              @ORM\Index (name="title", columns={"title"})
 *         }
 * )
 */
class SpecialOfferTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Special offer title.
     *
     * @var string
     * @ORM\Column (type="string", length=255)
     */
    protected $title;

    /**
     * Short promotional text.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $shortPromoText = '';

    /**
     * Full description.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $description = '';

    /**
     * Cart promotional text.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $cartPromoText = '';

    /**
     * Cart qualified text.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $cartAppliedText = '';

    /**
     * @var \QSL\SpecialOffersBase\Model\SpecialOffer
     *
     * @ORM\ManyToOne (targetEntity="QSL\SpecialOffersBase\Model\SpecialOffer", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="offer_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Sets the title that will be displayed for the special offer to customers.
     *
     * @param string $title New title
     *
     * @return SpecialOfferTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Returns the title that will be displayed for the special offer to customers.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the short promo text for the special offer.
     *
     * @param string $shortPromoText Short promo text
     *
     * @return SpecialOfferTranslation
     */
    public function setShortPromoText($shortPromoText)
    {
        $this->shortPromoText = $shortPromoText;

        return $this;
    }

    /**
     * Returns the short promo text for the special offer.
     *
     * @return string
     */
    public function getShortPromoText()
    {
        return $this->shortPromoText;
    }

    /**
     * Configures the description for the special offer.
     *
     * @param string $description Text
     *
     * @return SpecialOfferTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Returns the special offer description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Configures the promo text that will appear for the special offer on the cart page.
     *
     * @param string $cartPromoText Promo text
     *
     * @return SpecialOfferTranslation
     */
    public function setCartPromoText($cartPromoText)
    {
        $this->cartPromoText = $cartPromoText;

        return $this;
    }

    /**
     * Returns the promo text that will appear for the special offer on the cart page.
     *
     * @return string
     */
    public function getCartPromoText()
    {
        return $this->cartPromoText;
    }

    /**
     * Configures the promo text that will appear on the cart page for the special offer when it is applied.
     *
     * @param string $cartAppliedText Promo text
     *
     * @return SpecialOfferTranslation
     */
    public function setCartAppliedText($cartAppliedText)
    {
        $this->cartAppliedText = $cartAppliedText;

        return $this;
    }

    /**
     * Returns the promo text that should appear on the cart page for the special offer when it is applied.
     *
     * @return string
     */
    public function getCartAppliedText()
    {
        return $this->cartAppliedText;
    }

    /**
     * Returns the translation identifier.
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Sets the language code for the translation.
     *
     * @param string $code Code
     *
     * @return SpecialOfferTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Returns the language code for the translation.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
