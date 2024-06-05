<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * The "Product" decoration model class
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Model\Product
{
    /**
     * Order tabs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OrderBy   ({"position" = "ASC"})
     * @ORM\OneToMany (targetEntity="XC\CustomProductTabs\Model\Product\Tab", mappedBy="product", cascade={"all"})
     */
    protected $tabs;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->tabs = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone product
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        if ($this->getTabs()) {
            foreach ($this->getTabs() as $tab) {
                $newTab = $tab->cloneEntity();
                $newTab->setProduct($newProduct);
                $newProduct->addTabs($newTab);

                \XLite\Core\Database::getEM()->persist($newTab);
            }
        }

        return $newProduct;
    }

    /**
     * Add tabs
     *
     * @param \XC\CustomProductTabs\Model\Product\Tab $tabs
     * @return Product
     */
    public function addTabs(\XC\CustomProductTabs\Model\Product\Tab $tabs)
    {
        $this->tabs[] = $tabs;
        return $this;
    }

    /**
     * Get tabs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * Return GlobalTabs
     *
     * @return \XLite\Model\Product\GlobalTab[]
     */
    public function getGlobalTabs()
    {
        return [];
    }
}
