<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * Featured products (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="CDev\FeaturedProducts\Model\FeaturedProduct", mappedBy="category", cascade={"all"})
     */
    protected $featuredProducts;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->featuredProducts = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    public function getFeaturedProductsCount()
    {
        return $this->getFeaturedProducts()->count() ?: 0;
    }

    /**
     * Add featuredProducts
     *
     * @param \CDev\FeaturedProducts\Model\FeaturedProduct $featuredProducts
     * @return Category
     */
    public function addFeaturedProducts(\CDev\FeaturedProducts\Model\FeaturedProduct $featuredProducts)
    {
        $this->featuredProducts[] = $featuredProducts;
        return $this;
    }

    /**
     * Get featuredProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeaturedProducts()
    {
        return $this->featuredProducts;
    }
}
