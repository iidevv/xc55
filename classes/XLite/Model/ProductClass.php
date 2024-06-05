<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\ProductClass\DTO\ProductClassInput;
use XLite\API\Endpoint\ProductClass\DTO\ProductClassOutput;
use XLite\API\Filter\AlphabeticalOrderFilter;

/**
 * Product class
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_classes")
 * @ApiPlatform\ApiResource(
 *     shortName="Product Class",
 *     input=ProductClassInput::class,
 *     output=ProductClassOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/product_classes/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/product_classes/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/product_classes/{id}",
 *             "identifiers"={"id"},
 *         }
 *     },
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/product_classes",
 *             "identifiers"={"id"},
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/product_classes",
 *             "identifiers"={"id"},
 *         }
 *     }
 * )
 * @ApiPlatform\ApiFilter(AlphabeticalOrderFilter::class, properties={"name"="ASC"})
 */
class ProductClass extends \XLite\Model\Base\I18n
{
    /**
     * ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Attributes
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Attribute", mappedBy="productClass", cascade={"all"})
     */
    protected $attributes;

    /**
     * Attribute groups
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeGroup", mappedBy="productClass", cascade={"all"})
     * @ORM\OrderBy   ({"position" = "ASC"})
     */
    protected $attribute_groups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\ProductClassTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attribute_groups = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return number of products associated with the category
     *
     * @return integer
     */
    public function getProductsCount()
    {
        return $this->getProducts(null, true);
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition OPTIONAL
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    public function getProducts(\XLite\Core\CommonCell $cnd = null, $countOnly = false)
    {
        if ($this->isPersistent()) {
            if (!isset($cnd)) {
                $cnd = new \XLite\Core\CommonCell();
            }

            // Main condition for this search
            $cnd->{\XLite\Model\Repo\Product::P_PRODUCT_CLASS} = $this;

            $result = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, $countOnly);
        } else {
            $result = $countOnly ? 0 : [];
        }

        return $result;
    }

    /**
     * Return number of attributes associated with this class
     *
     * @return integer
     */
    public function getAttributesCount()
    {
        return count($this->getAttributes());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return ProductClass
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Add attributes
     *
     * @param \XLite\Model\Attribute $attributes
     * @return ProductClass
     */
    public function addAttributes(\XLite\Model\Attribute $attributes)
    {
        $this->attributes[] = $attributes;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add attribute_groups
     *
     * @param \XLite\Model\AttributeGroup $attributeGroups
     * @return ProductClass
     */
    public function addAttributeGroups(\XLite\Model\AttributeGroup $attributeGroups)
    {
        $this->attribute_groups[] = $attributeGroups;
        return $this;
    }

    /**
     * Get attribute_groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributeGroups()
    {
        return $this->attribute_groups;
    }
}
