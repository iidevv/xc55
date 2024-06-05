<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * PricegrabberCategory category model.
 *
 * @ORM\Entity (repositoryClass="\QSL\ProductFeeds\Model\Repo\PricegrabberCategory")
 * @ORM\Table  (name="pricegrabber_categories",
 *      indexes={
 *          @ORM\Index (name="name", columns={"name"})
 *      }
 * )
 */
class PricegrabberCategory extends \XLite\Model\AEntity
{
    /**
     * Pricegrabber category identifier.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", unique=true, options={ "unsigned": true })
     */
    protected $pricegrabber_id;

    /**
     * Category name.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Products added to the Pricegrabber category.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Product", mappedBy="pricegrabberCategory")
     */
    protected $products;

    /**
     * Constructor.
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }
    /**
     * Get object unique id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getPricegrabberId();
    }

    /**
     * Set the entity identifier.
     *
     * @param integer $pricegrabberId Identifier
     *
     * @return PricegrabberCategory
     */
    public function setPricegrabberId($pricegrabberId)
    {
        $this->pricegrabber_id = $pricegrabberId;

        return $this;
    }

    /**
     * Return thes entity identifier.
     *
     * @return integer
     */
    public function getPricegrabberId()
    {
        return $this->pricegrabber_id;
    }

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return PricegrabberCategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return PricegrabberCategory
     */
    public function addProducts(\XLite\Model\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
