<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table (name="bxgy_category_offers",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"offer_id","category_id"})
 *      },
 * )
 */
class CategoryOffer extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Relation to a category entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="bxgyCategoryOffers")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * Relation to an offer entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="QSL\SpecialOffersBase\Model\SpecialOffer", inversedBy="bxgyConditionCategories")
     * @ORM\JoinColumn (name="offer_id", referencedColumnName="offer_id", onDelete="CASCADE")
     */
    protected $offer;

    /**
     * Returns the entity identifier.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets a category for the relation.
     *
     * @param \XLite\Model\Category $category Category model.
     *
     * @return CategoryOffer
     */
    public function setCategory(\XLite\Model\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Returns the relation category.
     *
     * @return \XLite\Model\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets a special offer for the relation.
     *
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer Special Offer model
     *
     * @return CategoryOffer
     */
    public function setOffer(\QSL\SpecialOffersBase\Model\SpecialOffer $offer = null)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Returns the relation special offer.
     *
     * @return \QSL\SpecialOffersBase\Model\SpecialOffer
     */
    public function getOffer()
    {
        return $this->offer;
    }
}
