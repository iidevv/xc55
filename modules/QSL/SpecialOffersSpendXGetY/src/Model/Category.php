<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersSpendXGetY\Model;

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
        $this->sxgyCategoryOffers = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Relation to a CategoryOffers entities
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="\QSL\SpecialOffersSpendXGetY\Model\CategoryOffer", mappedBy="category", cascade={"all"})
     */
    protected $sxgyCategoryOffers;

    /**
     * Associates the category with a special offer through Category Offer model.
     *
     * @param \QSL\SpecialOffersSpendXGetY\Model\CategoryOffer $sxgyCategoryOffers Category Offer model
     *
     * @return Category
     */
    public function addSxgyCategoryOffers(\QSL\SpecialOffersSpendXGetY\Model\CategoryOffer $sxgyCategoryOffers)
    {
        $this->sxgyCategoryOffers[] = $sxgyCategoryOffers;

        return $this;
    }

    /**
     * Returns associated Category Offer models.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSxgyCategoryOffers()
    {
        return $this->sxgyCategoryOffers;
    }
}
