<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Special Offer model.
 *
 * @ORM\MappedSuperclass
 *
 * @ORM\Table (indexes={
 *      @ORM\Index (name="bxgyPromoCategory", columns={"bxgyPromoCategory"}),
 *  }
 * )
 * @Extender\Mixin
 */
class SpecialOffer extends \QSL\SpecialOffersBase\Model\SpecialOffer
{
    /**
     * Possible values for discountType property.
     */
    public const BXGY_DISCOUNT_TYPE_PERCENT = '%';
    public const BXGY_DISCOUNT_TYPE_FIXED   = '$';

    // Type-specific settings

    /**
     * Total number of items that trigger the special offer.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true, "default": 0 })
     */
    protected $bxgyN = 0;

    /**
     * Relation to a CategoryOffers entities
     *
     * @var \Doctrine\ORM\PersistentCollection
     *
     * @ORM\OneToMany (targetEntity="QSL\SpecialOffersBuyXGetY\Model\CategoryOffer", mappedBy="offer", cascade={"all"})
     */
    protected $bxgyConditionCategories;

    /**
     * Cached identifiers of $bxgyConditionCategories.
     *
     * @var array
     */
    protected $bxgyConditionCategoriesIds;

    /**
     * Identifiers of membership levels which are eligible for the offer.
     *
     * @var array
     *
     * @ORM\Column (type="array", nullable=true)
     */
    protected $bxgyConditionMemberships = [];

    /**
     * Number of items that receive the discount.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true, "default": 0 })
     */
    protected $bxgyM = 0;

    /**
     * Discount value.
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4, options={ "default": 0 })
     */
    protected $bxgyDiscount = 0;

    /**
     * Discount type (percent/fixed).
     *
     * self::BXGY_DISCOUNT_TYPE_FIXED   if the fee is a fixed sum.
     * self::BXGY_DISCOUNT_TYPE_PERCENT if the fee is a percent from the sum the custom is to pay for his order.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1, nullable=false, options={ "default": "$" })
     */
    protected $bxgyDiscountType = self::BXGY_DISCOUNT_TYPE_FIXED;

    /**
     * Whether the short promo text and image is displayed on matching category pages.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $bxgyPromoCategory = true;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->bxgyConditionCategories = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check if condition categories are configured for the special offer.
     *
     * @return boolean
     */
    public function hasBxgyConditionCategories()
    {
        return $this->getBxgyConditionCategories()->count() > 0;
    }

    /**
     * Check if the category is among condition categories.
     *
     * @param \XLite\Model\Category $category The category to check.
     *
     * @return boolean
     */
    public function isBxgyConditionCategory(\XLite\Model\Category $category)
    {
        return !$this->hasBxgyConditionCategories() || in_array($category->getCategoryId(), $this->getBxgyConditionCategoriesIds());
    }

    /**
     * Returns an array with IDs of condition categories.
     *
     * @return array
     */
    protected function getBxgyConditionCategoriesIds()
    {
        if (!isset($this->bxgyConditionCategoriesIds)) {
            $this->bxgyConditionCategoriesIds = $this->defineBxgyConditionCategoriesIds();
        }

        return $this->bxgyConditionCategoriesIds;
    }

    /**
     * Defines the array with IDs of condition categories.
     *
     * @return array
     */
    protected function defineBxgyConditionCategoriesIds()
    {
        $result = [];

        foreach ($this->getBxgyConditionCategories() as $categoryOffer) {
            $result[] = $categoryOffer->getCategory()->getCategoryId();
        }

        return $result;
    }

    /**
     * Set bxgyN
     *
     * @param integer $bxgyN
     * @return SpecialOffer
     */
    public function setBxgyN($bxgyN)
    {
        $this->bxgyN = $bxgyN;
        return $this;
    }

    /**
     * Get bxgyN
     *
     * @return integer
     */
    public function getBxgyN()
    {
        return $this->bxgyN;
    }

    /**
     * Set bxgyConditionMemberships
     *
     * @param array $bxgyConditionMemberships
     * @return SpecialOffer
     */
    public function setBxgyConditionMemberships($bxgyConditionMemberships)
    {
        $this->bxgyConditionMemberships = $bxgyConditionMemberships;
        return $this;
    }

    /**
     * Get bxgyConditionMemberships
     *
     * @return array
     */
    public function getBxgyConditionMemberships()
    {
        return $this->bxgyConditionMemberships;
    }

    /**
     * Configures the total number of items that trigger the special offer.
     *
     * @param integer $bxgyM Number of items
     *
     * @return SpecialOffer
     */
    public function setBxgyM($bxgyM)
    {
        $this->bxgyM = $bxgyM;

        return $this;
    }

    /**
     * Returns the total number of items that trigger the special offer.
     *
     * @return integer
     */
    public function getBxgyM()
    {
        return $this->bxgyM;
    }

    /**
     * Configures the discount amount.
     *
     * @param float $bxgyDiscount Discount amount
     *
     * @return SpecialOffer
     */
    public function setBxgyDiscount($bxgyDiscount)
    {
        $this->bxgyDiscount = $bxgyDiscount;

        return $this;
    }

    /**
     * Returns the discount amount.
     *
     * @return float
     */
    public function getBxgyDiscount()
    {
        return $this->bxgyDiscount;
    }

    /**
     * Configures the discount type.
     *
     * It should be one of the following:
     * - self::BXGY_DISCOUNT_TYPE_FIXED   if the fee is a fixed sum.
     * - self::BXGY_DISCOUNT_TYPE_PERCENT if the fee is a percent from the sum the custom is to pay for his order.
     *
     * @param string $bxgyDiscountType Discount type
     * @return SpecialOffer
     */
    public function setBxgyDiscountType($bxgyDiscountType)
    {
        $this->bxgyDiscountType = $bxgyDiscountType;

        return $this;
    }

    /**
     * Returns the discount type.
     *
     * It can be one of these:
     * - self::BXGY_DISCOUNT_TYPE_FIXED   if the fee is a fixed sum.
     * - self::BXGY_DISCOUNT_TYPE_PERCENT if the fee is a percent from the sum the custom is to pay for his order.
     *
     * @return string
     */
    public function getBxgyDiscountType()
    {
        return $this->bxgyDiscountType;
    }

    /**
     * Configures whether the short promo text and image is displayed on matching category pages.
     *
     * @param boolean $bxgyPromoCategory Flag
     *
     * @return SpecialOffer
     */
    public function setBxgyPromoCategory($bxgyPromoCategory)
    {
        $this->bxgyPromoCategory = $bxgyPromoCategory;

        return $this;
    }

    /**
     * Checks if the short promo text and image should be displayed on matching category pages, or not.
     *
     * @return boolean
     */
    public function getBxgyPromoCategory()
    {
        return $this->bxgyPromoCategory;
    }

    /**
     * Associates the special offer with a condition category through a CategoryOffers entity.
     *
     * @param QSL\SpecialOffersBuyXGetY\Model\CategoryOffer $bxgyConditionCategories CategoryOffer relation
     * @return SpecialOffer
     */
    public function addBxgyConditionCategories(\QSL\SpecialOffersBuyXGetY\Model\CategoryOffer $bxgyConditionCategories)
    {
        $this->bxgyConditionCategories[] = $bxgyConditionCategories;

        return $this;
    }

    /**
     * Returns associated CategoryOffer relations.
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getBxgyConditionCategories()
    {
        return $this->bxgyConditionCategories;
    }
}
