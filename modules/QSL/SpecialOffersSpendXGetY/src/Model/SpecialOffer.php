<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersSpendXGetY\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Special Offer model.
 *
 * @ORM\MappedSuperclass
 *
 * @ORM\Table (indexes={
 *      @ORM\Index (name="sxgyPromoCategory", columns={"sxgyPromoCategory"}),
 *  }
 * )
 * @Extender\Mixin
 */
class SpecialOffer extends \QSL\SpecialOffersBase\Model\SpecialOffer
{
    /**
     * Possible values for discountType property.
     */
    public const SXGY_DISCOUNT_TYPE_PERCENT = '%';
    public const SXGY_DISCOUNT_TYPE_FIXED   = '$';

    // Type-specific settings

    /**
     * The order subtotal that should trigger the special offer.
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4, options={ "default": 0 })
     */
    protected $sxgyT = 0;

    /**
     * Relation to a CategoryOffers entities
     *
     * @var \Doctrine\ORM\PersistentCollection
     *
     * @ORM\OneToMany (targetEntity="QSL\SpecialOffersSpendXGetY\Model\CategoryOffer", mappedBy="offer", cascade={"all"})
     */
    protected $sxgyConditionCategories;

    /**
     * Cached identifiers of $sxgyConditionCategories.
     *
     * @var array
     */
    protected $sxgyConditionCategoriesIds;

    /**
     * Identifiers of membership levels which are eligible for the offer.
     *
     * @var array
     *
     * @ORM\Column (type="array", nullable=true)
     */
    protected $sxgyConditionMemberships = [];

    /**
     * Number of items that receive the discount.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true, "default": 0 })
     */
    protected $sxgyM = 0;

    /**
     * Discount value.
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4, options={ "default": 0 })
     */
    protected $sxgyDiscount = 0;

    /**
     * Discount type (percent/fixed).
     *
     * self::SXGY_DISCOUNT_TYPE_FIXED   if the fee is a fixed amount.
     * self::SXGY_DISCOUNT_TYPE_PERCENT if the fee is a percent from the sum the custom is to pay for his order.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1, nullable=false, options={ "default": "$" })
     */
    protected $sxgyDiscountType = self::SXGY_DISCOUNT_TYPE_FIXED;

    /**
     * Whether the short promo text and image is displayed on matching category pages.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $sxgyPromoCategory = true;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->sxgyConditionCategories = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check if condition categories are configured for the special offer.
     *
     * @return boolean
     */
    public function hasSxgyConditionCategories()
    {
        return $this->getSxgyConditionCategories()->count() > 0;
    }

    /**
     * Check if the category is among condition categories.
     *
     * @param \XLite\Model\Category $category The category to check.
     *
     * @return boolean
     */
    public function isSxgyConditionCategory(\XLite\Model\Category $category)
    {
        return !$this->hasSxgyConditionCategories() || in_array($category->getCategoryId(), $this->getSxgyConditionCategoriesIds());
    }

    /**
     * Returns an array with IDs of condition categories.
     *
     * @return array
     */
    protected function getSxgyConditionCategoriesIds()
    {
        if (!isset($this->sxgyConditionCategoriesIds)) {
            $this->sxgyConditionCategoriesIds = $this->defineSxgyConditionCategoriesIds();
        }

        return $this->sxgyConditionCategoriesIds;
    }

    /**
     * Defines the array with IDs of condition categories.
     *
     * @return array
     */
    protected function defineSxgyConditionCategoriesIds()
    {
        $result = [];

        foreach ($this->getSxgyConditionCategories() as $categoryOffer) {
            $result[] = $categoryOffer->getCategory()->getCategoryId();
        }

        return $result;
    }

    /**
     * Sets the order subtotal that should trigger the special offer.
     *
     * @param float $sxgyT Minumum order subtotal
     *
     * @return SpecialOffer
     */
    public function setSxgyT($sxgyT)
    {
        $this->sxgyT = $sxgyT;

        return $this;
    }

    /**
     * Returns the order subtotal that should trigger the special offer.
     *
     * @return integer
     */
    public function getSxgyT()
    {
        return $this->sxgyT;
    }

    /**
     * Configures membership levels which are eligible for the offer.
     *
     * @param array $sxgyConditionMemberships Membership levels
     *
     * @return SpecialOffer
     */
    public function setSxgyConditionMemberships($sxgyConditionMemberships)
    {
        $this->sxgyConditionMemberships = $sxgyConditionMemberships;

        return $this;
    }

    /**
     * Returns membership levels which are eligible for the offer.
     *
     * @return array
     */
    public function getSxgyConditionMemberships()
    {
        return $this->sxgyConditionMemberships;
    }

    /**
     * Sets the number of items that receive the discount.
     *
     * @param integer $sxgyM Number of items
     *
     * @return SpecialOffer
     */
    public function setSxgyM($sxgyM)
    {
        $this->sxgyM = $sxgyM;

        return $this;
    }

    /**
     * Returns the number of items that receive the discount.
     *
     * @return integer
     */
    public function getSxgyM()
    {
        return $this->sxgyM;
    }

    /**
     * Configures the discount amount.
     *
     * @param float $sxgyDiscount Discount amount
     *
     * @return SpecialOffer
     */
    public function setSxgyDiscount($sxgyDiscount)
    {
        $this->sxgyDiscount = $sxgyDiscount;

        return $this;
    }

    /**
     * Returns the discount amount.
     *
     * @return float
     */
    public function getSxgyDiscount()
    {
        return $this->sxgyDiscount;
    }

    /**
     * Configures the discount type.
     *
     * It should be one of the following:
     * - self::SXGY_DISCOUNT_TYPE_FIXED   if the fee is a fixed amount.
     * - self::SXGY_DISCOUNT_TYPE_PERCENT if the fee is a percent from the sum the custom is to pay for his order.
     *
     * @param string $sxgyDiscountType Discount type
     *
     * @return SpecialOffer
     */
    public function setSxgyDiscountType($sxgyDiscountType)
    {
        $this->sxgyDiscountType = $sxgyDiscountType;

        return $this;
    }

    /**
     * Returns the discount type.
     *
     * It can be on of these:
     * - self::SXGY_DISCOUNT_TYPE_FIXED   if the fee is a fixed amount.
     * - self::SXGY_DISCOUNT_TYPE_PERCENT if the fee is a percent from the sum the custom is to pay for his order.
     *
     * @return string
     */
    public function getSxgyDiscountType()
    {
        return $this->sxgyDiscountType;
    }

    /**
     * Configures whether the short promo text and image is displayed on matching category pages.
     *
     * @param boolean $sxgyPromoCategory Flag
     *
     * @return SpecialOffer
     */
    public function setSxgyPromoCategory($sxgyPromoCategory)
    {
        $this->sxgyPromoCategory = $sxgyPromoCategory;

        return $this;
    }

    /**
     * Checks if the short promo text and image should be displayed on matching category pages, or not.
     *
     * @return boolean
     */
    public function getSxgyPromoCategory()
    {
        return $this->sxgyPromoCategory;
    }

    /**
     * Associates the special offer with a condition category through a CategoryOffers entity.
     *
     * @param QSL\SpecialOffersSpendXGetY\Model\CategoryOffer $sxgyConditionCategories CategoryOffer relation
     *
     * @return SpecialOffer
     */
    public function addSxgyConditionCategories(\QSL\SpecialOffersSpendXGetY\Model\CategoryOffer $sxgyConditionCategories)
    {
        $this->sxgyConditionCategories[] = $sxgyConditionCategories;

        return $this;
    }

    /**
     * Returns associated CategoryOffer relations.
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSxgyConditionCategories()
    {
        return $this->sxgyConditionCategories;
    }
}
