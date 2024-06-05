<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\AttributeValue\Checkbox\DTO\AttributeValueCheckboxInput as Input;
use XLite\API\Endpoint\AttributeValue\Checkbox\DTO\AttributeValueCheckboxOutput as Output;
use XLite\Controller\API\AttributeValue\Checkbox\Post as PostController;

/**
 * Attribute value (checkbox)
 *
 * @ORM\Entity
 * @ORM\Table (name="attribute_values_checkbox",
 *      indexes={
 *          @ORM\Index (name="product_id", columns={"product_id"}),
 *          @ORM\Index (name="attribute_id", columns={"attribute_id"}),
 *          @ORM\Index (name="value", columns={"value"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     attributes={"pagination_enabled": false},
 *     shortName="Yes/No Attribute Value",
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_checkbox/{attribute_id}/values/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id", "id"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a value from a product yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/attributes_checkbox/{attribute_id}/values/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id", "id"},
 *              "openapi_context"={
 *                  "summary"="Update a value of a product yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/attributes_checkbox/{attribute_id}/values/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+", "id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id", "id"},
 *              "openapi_context"={
 *                  "summary"="Delete a value from a product yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/attributes_checkbox/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of values from a product yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              },
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/attributes_checkbox/{attribute_id}/values.{_format}",
 *              "identifiers"={"id"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "controller"=PostController::class,
 *              "requirements"={"product_id"="\d+", "attribute_id"="\d+"},
 *              "identifiers"={"product_id", "attribute_id"},
 *              "openapi_context"={
 *                  "summary"="Add a value to a product yes/no attribute",
 *                  "parameters"={
 *                     {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                     {"name"="attribute_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class AttributeValueCheckbox extends \XLite\Model\AttributeValue\Multiple
{
    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product", inversedBy="attributeValueC")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Value
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $value = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueCheckboxTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        return (string)static::t($this->getValue() ? 'Yes' : 'No');
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
     * @return boolean
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        if ($value === 'Y' || $value === 1) {
            $value = true;
        } elseif ($value === 'N' || $value === 0) {
            $value = false;
        }

        $this->value = $value;
    }
}
