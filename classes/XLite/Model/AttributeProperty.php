<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\AttributeProperty\DTO\AttributePropertyInput as Input;
use XLite\API\Endpoint\AttributeProperty\DTO\AttributePropertyOutput as Output;
use XLite\Controller\API\AttributeProperty\Post as PostController;

/**
 * @ORM\Entity
 * @ORM\Table  (name="attribute_properties")
 * @ApiPlatform\ApiResource(
 *     shortName="Attribute Property",
 *     compositeIdentifier=false,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes/{attribute_id}/property.{_format}",
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a property from a product attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes/{attribute_id}/property.{_format}",
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Update a property of a product attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "post"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/attributes/{attribute_id}/property.{_format}",
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "controller"=PostController::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Add a property to a product attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class AttributeProperty extends \XLite\Model\AEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @var \XLite\Model\Attribute
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Attribute", inversedBy="attribute_properties")
     * @ORM\JoinColumn (name="attribute_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Is attribute shown above the price
     *
     * @var bool
     *
     * @ORM\Column (type="boolean", options={"default":"0"})
     */
    protected $displayAbove = false;

    /**
     * @var string
     *
     * @ORM\Column (type="string", options={"fixed": true}, length=1)
     */
    protected $displayMode = '';

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
     * @return AttributeProperty
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
     * @param boolean $displayAbove
     * @return AttributeProperty
     */
    public function setDisplayAbove($displayAbove)
    {
        $this->displayAbove = $displayAbove;
        return $this;
    }

    /**
     * @return integer
     */
    public function getDisplayAbove()
    {
        return $this->displayAbove;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return AttributeProperty
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set attribute
     *
     * @param \XLite\Model\Attribute $attribute
     * @return AttributeProperty
     */
    public function setAttribute(\XLite\Model\Attribute $attribute = null)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute
     *
     * @return \XLite\Model\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Get display mode
     *
     * @return string
     */
    public function getDisplayMode()
    {
        return $this->displayMode;
    }

    /**
     * Set display mode
     *
     * @return AttributeProperty
     */
    public function setDisplayMode($displayMode)
    {
        $this->displayMode = $displayMode;

        return $this;
    }
}
