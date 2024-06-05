<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category model
 *
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * Banners (relation)
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="QSL\Banner\Model\Banner", mappedBy="categories",  cascade={"merge","detach"})
     */
    protected $banners;


    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->banners = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add memberships
     *
     * @param \QSL\Banner\Model\Banner $banners
     * @return Category
     */
    public function addBanners(\QSL\Banner\Model\Banner $banners)
    {
        $this->banners[] = $banners;
        return $this;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanners()
    {
        return $this->banners;
    }
}
