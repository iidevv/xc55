<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\AttributeValue\Hidden\DTO\AttributeValueHiddenInput as Input;
use XLite\API\Endpoint\AttributeValue\Hidden\DTO\AttributeValueHiddenOutput as Output;
use XLite\Controller\API\AttributeValue\Hidden\Post as PostController;

/**
 * Attribute value (hidden)
 *
 * @ORM\Entity
 * @ORM\Table  (name="attribute_values_hidden",
 *      indexes={
 *          @ORM\Index (name="product_id", columns={"product_id"}),
 *          @ORM\Index (name="attribute_id", columns={"attribute_id"}),
 *          @ORM\Index (name="attribute_option_id", columns={"attribute_option_id"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     attributes={"pagination_enabled": false},
 *     compositeIdentifier=false,
 *     shortName="Hidden Attribute Value",
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_hidden/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a value from a product hidden attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes_hidden/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Update a value of a product hidden attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attributes_hidden/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Delete a value from a product hidden attribute",
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
 *              "path"="/products/{product_id}/attributes_hidden/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "controller"=PostController::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add a value to a product hidden attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class AttributeValueHidden extends \XLite\Model\AttributeValue\AAttributeValue
{
    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product", inversedBy="attributeValueH")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Attribute option
     *
     * @var \XLite\Model\AttributeOption
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\AttributeOption")
     * @ORM\JoinColumn (name="attribute_option_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute_option;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueHiddenTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        /** @see \XLite\Model\AttributeOptionTranslation */
        return $this->getAttributeOption()->getName();
    }

    /**
     * Clone
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $newEntity */
        $newEntity = parent::cloneEntity();

        if ($this->getAttributeOption()) {
            $attributeOption = $this->getAttributeOption();
            $newEntity->setAttributeOption($attributeOption);
        }

        return $newEntity;
    }

    /**
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \XLite\Model\Product $product
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
    }

    /**
     * @param \XLite\Model\AttributeOption $attributeOption
     *
     * @return static
     */
    public function setAttributeOption(\XLite\Model\AttributeOption $attributeOption = null)
    {
        $this->attribute_option = $attributeOption;

        return $this;
    }

    /**
     * @return \XLite\Model\AttributeOption
     */
    public function getAttributeOption()
    {
        return $this->attribute_option;
    }
}
