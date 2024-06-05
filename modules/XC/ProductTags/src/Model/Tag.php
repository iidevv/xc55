<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Filter\AlphabeticalOrderFilter;
use XC\ProductTags\API\Endpoint\Tag\DTO\TagInput as ProductTagInput;
use XC\ProductTags\API\Endpoint\Tag\DTO\TagOutput as ProductTagOutput;

/**
 * @ORM\Entity
 * @ORM\Table (name="tags")
 * @ORM\HasLifecycleCallbacks
 * @ApiPlatform\ApiResource(
 *     shortName="Product Tag",
 *     input=ProductTagInput::class,
 *     output=ProductTagOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/product_tags/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/product_tags/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/product_tags/{id}",
 *             "identifiers"={"id"},
 *         }
 *     },
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/product_tags",
 *             "identifiers"={"id"},
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/product_tags",
 *             "identifiers"={"id"},
 *         }
 *     }
 * )
 * @ApiPlatform\ApiFilter(AlphabeticalOrderFilter::class, properties={"name"="ASC"})
 */
class Tag extends \XLite\Model\Base\I18n
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Product", mappedBy="tags")
     */
    protected $products;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\ProductTags\Model\TagTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @deprecated 5.4
     * @see addProduct
     *
     * @param \XLite\Model\Product $products
     * @return Tag
     */
    public function addProducts(\XLite\Model\Product $products)
    {
        return $this->addProduct($products);
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return Tag
     */
    public function addProduct(\XLite\Model\Product $product)
    {
        if (
            !$this->products
            || !$this->products->contains($product)
        ) {
            $this->products[] = $product;
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
