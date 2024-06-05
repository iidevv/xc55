<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nextag category model.
 *
 * @ORM\Entity (repositoryClass="\QSL\ProductFeeds\Model\Repo\NextagCategory")
 * @ORM\Table  (name="nextag_categories",
 *      indexes={
 *          @ORM\Index (name="name", columns={"name"})
 *      }
 * )
 */
class NextagCategory extends \XLite\Model\AEntity
{
    /**
     * Nextag category identifier.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", unique=true, options={ "unsigned": true })
     */
    protected $nextag_id;

    /**
     * Category name.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Products added to the Nextag category.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Product", mappedBy="nextagCategory")
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
        return $this->getNextagId();
    }

    /**
     * Set the entity identifier.
     *
     * @param integer $nextagId Identifier
     *
     * @return NextagCategory
     */
    public function setNextagId($nextagId)
    {
        $this->nextag_id = $nextagId;

        return $this;
    }

    /**
     * Returns the entity identifier.
     *
     * @return integer
     */
    public function getNextagId()
    {
        return $this->nextag_id;
    }

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return NextagCategory
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
     * @return NextagCategory
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
