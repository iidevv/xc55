<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category model.
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->bxgyCategoryOffers = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Relation to a CategoryOffers entities
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="\QSL\SpecialOffersBuyXGetY\Model\CategoryOffer", mappedBy="category", cascade={"all"})
     */
    protected $bxgyCategoryOffers;

    /**
     * Associates the category with a special offer through Category Offer model.
     *
     * @param \QSL\SpecialOffersBuyXGetY\Model\CategoryOffer $bxgyCategoryOffers Category Offer model
     *
     * @return Category
     */
    public function addBxgyCategoryOffers(\QSL\SpecialOffersBuyXGetY\Model\CategoryOffer $bxgyCategoryOffers)
    {
        $this->bxgyCategoryOffers[] = $bxgyCategoryOffers;

        return $this;
    }

    /**
     * Returns associated Category Offer models.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBxgyCategoryOffers()
    {
        return $this->bxgyCategoryOffers;
    }
}
